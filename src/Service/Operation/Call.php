<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Data\Entity\Operation as EntityOperation;

class Call extends \Praxigento\Core\Service\Base\Call implements \Praxigento\Accounting\Service\IOperation
{
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Accounting\Repo\Entity\IOperation */
    protected $_repoOper;
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\IOperation */
    protected $_repoTypeOper;
    /** @var Sub\Add */
    protected $_subAdd;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\IOperation $repoOper,
        \Praxigento\Accounting\Repo\Entity\Type\IOperation $repoTypeOper,
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
        $def = $this->_manTrans->begin();
        try {
            /* add operation itself */
            if (!$operationTypeId) {
                $operationTypeId = $this->_repoTypeOper->getIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                EntityOperation::ATTR_TYPE_ID => $operationTypeId,
                EntityOperation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            $idCreated = $this->_repoOper->create($bindToAdd);
            if ($idCreated) {
                $transIds = $this->_subAdd->transactions($idCreated, $transactions, $datePerformed, $asRef);
                $result->setOperationId($idCreated);
                $result->setTransactionsIds($transIds);
                $this->_manTrans->commit($def);
                $result->markSucceed();
            }
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }
}