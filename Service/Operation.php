<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;


use Praxigento\Accounting\Api\Service\Operation\Create\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Response as AResponse;
use Praxigento\Accounting\Repo\Data\Operation as EOperation;


class Operation
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
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Service\Operation\Add */
    private $subAdd;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Repo\Dao\Operation $daoOper,
        \Praxigento\Accounting\Repo\Dao\Type\Operation $daoTypeOper,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Admin $daoELogChangeAdmin,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Customer $daoELogChangeCust,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Service\Operation\Add $subAdd
    ) {
        $this->manTrans = $manTrans;
        $this->daoTypeOper = $daoTypeOper;
        $this->daoOper = $daoOper;
        $this->daoELogChangeAdmin = $daoELogChangeAdmin;
        $this->daoELogChangeCust = $daoELogChangeCust;
        $this->hlpDate = $hlpDate;
        $this->subAdd = $subAdd;
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
        /* Better this nested transaction will break outer transaction than we will clean up data manually */
        $def = $this->manTrans->begin();
        try {
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
            $operId = $this->daoOper->create($bindToAdd);
            if ($operId) {
                $transIds = $this->subAdd->exec($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
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
            $this->manTrans->end($def);
        }
        return $result;
    }

}