<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Operation;


use Praxigento\Accounting\Api\Service\Operation\Create\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Response as AResponse;
use Praxigento\Accounting\Repo\Data\Operation as EOperation;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;


class Create
    implements \Praxigento\Accounting\Api\Service\Operation\Create
{
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Admin */
    private $daoELogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Customer */
    private $daoELogChangeCust;
    /** @var  \Praxigento\Accounting\Repo\Dao\Operation */
    private $daoOper;
    /** @var  \Praxigento\Accounting\Repo\Dao\Type\Operation */
    private $daoTypeOper;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /**
     * Don't delete transaction from this service.
     * Better this service will break outer transaction than we will clean up data manually.
     *
     * @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager
     */
    private $manTrans;
    /** @var \Praxigento\Accounting\Service\Operation\Create\A\Add */
    private $ownAdd;
    /** @var \Magento\Backend\Model\Auth\Session */
    private $sessAdmin;
    /** @var \Magento\Customer\Model\Session */
    private $sessCust;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $sessAdmin,
        \Magento\Customer\Model\Session $sessCust,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Repo\Dao\Operation $daoOper,
        \Praxigento\Accounting\Repo\Dao\Type\Operation $daoTypeOper,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Admin $daoELogChangeAdmin,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Customer $daoELogChangeCust,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Service\Operation\Create\A\Add $ownAdd
    ) {
        $this->sessAdmin = $sessAdmin;
        $this->sessCust = $sessCust;
        $this->logger = $logger;
        $this->manTrans = $manTrans;
        $this->daoTypeOper = $daoTypeOper;
        $this->daoOper = $daoOper;
        $this->daoELogChangeAdmin = $daoELogChangeAdmin;
        $this->daoELogChangeCust = $daoELogChangeCust;
        $this->hlpDate = $hlpDate;
        $this->ownAdd = $ownAdd;
    }

    /**
     * Add operation with list of transactions and change account balances.
     *
     * @param ARequest $request
     *
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $operationTypeId = $request->getOperationTypeId();
        $operationTypeCode = $request->getOperationTypeCode();
        $datePerformed = $request->getDatePerformed();
        $note = $request->getOperationNote();
        $transactions = $request->getTransactions();
        $asRef = $request->getAsTransRef();
        $customerId = $request->getCustomerId();
        $adminUserId = $request->getAdminUserId();


        if (empty($datePerformed)) {
            $datePerformed = $this->hlpDate->getUtcNowForDb();
        }
        /* add operation itself */
        if (!$operationTypeId) {
            $operationTypeId = $this->daoTypeOper->getIdByCode($operationTypeCode);
        }
        $bindToAdd = [
            EOperation::A_TYPE_ID => $operationTypeId,
            EOperation::A_DATE_PREFORMED => $datePerformed
        ];
        if (!is_null($note)) {
            $bindToAdd[EOperation::A_NOTE] = $note;
        }
        $validTrans = $this->validateTransactions($transactions);
        if ($validTrans == AResponse::ERR_NO_ERROR) {
            /**
             * Better this nested transaction will break outer transaction than we will clean up data manually.
             */
            $def = $this->manTrans->begin();
            try {

                $operId = $this->daoOper->create($bindToAdd);
                if ($operId) {
                    $transIds = $this->ownAdd->exec($operId, $transactions, $datePerformed, $asRef);
                    $result->setOperationId($operId);
                    $result->setTransactionsIds($transIds);
                    /* extract IDs from sessions if both are empty */
                    list($customerId, $adminUserId) = $this->prepareLogIds($customerId, $adminUserId);
                    /* log customer link */
                    if ($customerId) {
                        $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Customer();
                        $log->setCustomerRef($customerId);
                        $log->setOperationRef($operId);
                        $this->daoELogChangeCust->create($log);
                    }
                    /* log admin link */
                    if ($adminUserId) {
                        $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Admin();
                        $log->setUserRef($adminUserId);
                        $log->setOperationRef($operId);
                        $this->daoELogChangeAdmin->create($log);
                    }
                    $this->manTrans->commit($def);
                    $result->markSucceed();
                }

            } finally {
                /* rollback transaction if not committed (on exception, for example) */
                $this->manTrans->end($def);
            }
        } else {
            $dump = var_export($request, true);
            $this->logger->error("Validation error: $validTrans. Details: $dump.");
            $result->setErrorCode($validTrans);
        }
        return $result;
    }

    /**
     * Extract IDs from sessions if both are empty.
     *
     * @param int $custId .
     * @param int $adminId
     * @return int[]
     */
    private function prepareLogIds($custId, $adminId)
    {

        if (!$custId && !$adminId) {
            /* both are empty */
            $customer = $this->sessCust->getCustomer();
            if ($customer) {
                $custId = $customer->getId();
            }
            $user = $this->sessAdmin->getUser();
            if ($user instanceof \Magento\User\Model\User) {
                $adminId = $user->getId();
            }
        }
        return [$custId, $adminId];
    }

    /**
     * Validate transactions.
     *
     * @param ETrans[] $trans
     * @return string
     */
    private function validateTransactions($trans)
    {
        $result = AResponse::ERR_NO_ERROR;
        foreach ($trans as $one) {
            $accDbt = $one->getDebitAccId();
            $accCrd = $one->getCreditAccId();
            if ($accDbt == $accCrd) {
                $result = AResponse::ERR_ONE_ACCOUNT_FOR_DEBIT_AND_CREDIT;
                break;
            }
        }
        return $result;
    }
}