<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;


use Praxigento\Accounting\Api\Service\Operation\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Response as AResponse;
use Praxigento\Accounting\Repo\Entity\Data\Operation as EOperation;


class Operation
    implements \Praxigento\Accounting\Api\Service\Operation
{
    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    private $_manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Admin */
    private $_repoELogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Customer */
    private $_repoELogChangeCust;
    /** @var  \Praxigento\Accounting\Repo\Entity\Operation */
    private $_repoOper;
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Operation */
    private $_repoTypeOper;
    /** @var \Praxigento\Accounting\Service\Operation\Add */
    private $_subAdd;

    public function __construct(
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\Operation $repoOper,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Admin $repoELogChangeAdmin,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Customer $repoELogChangeCust,
        \Praxigento\Accounting\Service\Operation\Add $subAdd
    )
    {
        $this->_manTrans = $manTrans;
        $this->_repoTypeOper = $repoTypeOper;
        $this->_repoOper = $repoOper;
        $this->_repoELogChangeAdmin = $repoELogChangeAdmin;
        $this->_repoELogChangeCust = $repoELogChangeCust;
        $this->_subAdd = $subAdd;
    }

    /**
     * Add operation with list of transactions and change account balances.
     *
     * @param ARequest $req
     *
     * @return AResponse
     */
    public function exec(ARequest $req)
    {
        $result = new AResponse();
        $operationTypeId = $req->getOperationTypeId();
        $operationTypeCode = $req->getOperationTypeCode();
        $datePerformed = $req->getDatePerformed();
        $note = $req->getOperationNote();
        $transactions = $req->getTransactions();
        $asRef = $req->getAsTransRef();
        $customerId = $req->getCustomerId();
        $adminUserId = $req->getAdminUserId();
        $def = $this->_manTrans->begin();
        try {
            /* add operation itself */
            if (!$operationTypeId) {
                $operationTypeId = $this->_repoTypeOper->getIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                EOperation::ATTR_TYPE_ID => $operationTypeId,
                EOperation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            if (!is_null($note)) {
                $bindToAdd[EOperation::ATTR_NOTE] = $note;
            }
            $operId = $this->_repoOper->create($bindToAdd);
            if ($operId) {
                $transIds = $this->_subAdd->exec($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
                /* log customer link */
                if ($customerId) {
                    $log = new \Praxigento\Accounting\Repo\Entity\Data\Log\Change\Customer();
                    $log->setCustomerRef($customerId);
                    $log->setOperationRef($operId);
                    $this->_repoELogChangeCust->create($log);
                }
                /* log admin link */
                if ($adminUserId) {
                    $log = new \Praxigento\Accounting\Repo\Entity\Data\Log\Change\Admin();
                    $log->setUserRef($adminUserId);
                    $log->setOperationRef($operId);
                    $this->_repoELogChangeAdmin->create($log);
                }
                $this->_manTrans->commit($def);
                $result->markSucceed();
            }
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }

}