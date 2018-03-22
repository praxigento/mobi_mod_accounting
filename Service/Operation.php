<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;


use Praxigento\Accounting\Api\Service\Operation\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Response as AResponse;
use Praxigento\Accounting\Repo\Data\Operation as EOperation;


class Operation
    implements \Praxigento\Accounting\Api\Service\Operation
{
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Admin */
    private $repoELogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Customer */
    private $repoELogChangeCust;
    /** @var  \Praxigento\Accounting\Repo\Dao\Operation */
    private $repoOper;
    /** @var  \Praxigento\Accounting\Repo\Dao\Type\Operation */
    private $repoTypeOper;
    /** @var \Praxigento\Accounting\Service\Operation\Add */
    private $subAdd;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Repo\Dao\Operation $repoOper,
        \Praxigento\Accounting\Repo\Dao\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Admin $repoELogChangeAdmin,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Customer $repoELogChangeCust,
        \Praxigento\Accounting\Service\Operation\Add $subAdd
    ) {
        $this->manTrans = $manTrans;
        $this->repoTypeOper = $repoTypeOper;
        $this->repoOper = $repoOper;
        $this->repoELogChangeAdmin = $repoELogChangeAdmin;
        $this->repoELogChangeCust = $repoELogChangeCust;
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
        $def = $this->manTrans->begin();
        try {
            /* add operation itself */
            if (!$operationTypeId) {
                $operationTypeId = $this->repoTypeOper->getIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                EOperation::ATTR_TYPE_ID => $operationTypeId,
                EOperation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            if (!is_null($note)) {
                $bindToAdd[EOperation::ATTR_NOTE] = $note;
            }
            $operId = $this->repoOper->create($bindToAdd);
            if ($operId) {
                $transIds = $this->subAdd->exec($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
                /* log customer link */
                if ($customerId) {
                    $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Customer();
                    $log->setCustomerRef($customerId);
                    $log->setOperationRef($operId);
                    $this->repoELogChangeCust->create($log);
                }
                /* log admin link */
                if ($adminUserId) {
                    $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Admin();
                    $log->setUserRef($adminUserId);
                    $log->setOperationRef($operId);
                    $this->repoELogChangeAdmin->create($log);
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