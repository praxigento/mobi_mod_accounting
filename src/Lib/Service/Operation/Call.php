<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Service\Operation;

use Praxigento\Accounting\Data\Entity\Operation as EntityOperation;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements \Praxigento\Accounting\Lib\Service\IOperation
{
    /** @var Sub\Add */
    protected $_subAdd;
    /** @var  \Praxigento\Accounting\Repo\IOperation */
    protected $_repoOper;
    /** @var  \Praxigento\Accounting\Repo\Type\IOperation */
    protected $_repoTypeOper;
    /** @var  \Praxigento\Core\Lib\Context\ITransactionManager */
    protected $_manTrans;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Lib\Context\ITransactionManager $manTrans,
        \Praxigento\Accounting\Repo\IOperation $repoOper,
        \Praxigento\Accounting\Repo\Type\IOperation $repoTypeOper,
        Sub\Add $subAdd
    ) {
        parent::__construct($logger);
        $this->_manTrans = $manTrans;
        $this->_repoTypeOper = $repoTypeOper;
        $this->_repoOper = $repoOper;
        $this->_subAdd = $subAdd;
    }

    /**
     * Add operation with list of transactions and change account balances.
     *
     * @param Request\Add $req
     *
     * @return Response\Add
     */
    public function add(Request\Add $req)
    {
        $result = new Response\Add();
        $operationTypeId = $req->getOperationTypeId();
        $operationTypeCode = $req->getOperationTypeCode();
        $datePerformed = $req->getDatePerformed();
        $transactions = $req->getTransactions();
        $asRef = $req->getAsTransRef();
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* add operation itself */
            if (!$operationTypeId) {
                $operationTypeId = $this->_repoTypeOper->getIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                EntityOperation::ATTR_TYPE_ID => $operationTypeId,
                EntityOperation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            $created = $this->_repoOper->create($bindToAdd);
            if ($created && isset($created[EntityOperation::ATTR_ID])) {
                $operId = $created[EntityOperation::ATTR_ID];
                $transIds = $this->_subAdd->transactions($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
                $this->_manTrans->transactionCommit($trans);
                $result->setAsSucceed();
            }
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }
}