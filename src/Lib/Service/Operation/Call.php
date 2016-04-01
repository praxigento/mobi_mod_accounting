<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Service\Operation;

use Praxigento\Accounting\Data\Entity\Operation;
use Praxigento\Core\Lib\Service\Repo\Request\AddEntity as AddEntityRequest;
use Praxigento\Core\Lib\Service\Repo\Response\AddEntity as AddEntityResponse;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements \Praxigento\Accounting\Lib\Service\IOperation {
    /** @var  \Praxigento\Accounting\Lib\Service\Type\Operation\Call */
    protected $_callTypeOperation;
    /** @var Sub\Add */
    protected $_subAdd;
    /** @var \Praxigento\Accounting\Lib\Repo\IModule */
    protected $_repoMod;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Lib\Context\IDbAdapter $dba,
        \Praxigento\Core\Lib\IToolbox $toolbox,
        \Praxigento\Core\Lib\Service\IRepo $callRepo,
        \Praxigento\Accounting\Lib\Service\Type\Operation\Call $callTypeOperation,
        \Praxigento\Accounting\Lib\Repo\IModule $repoMod,
        Sub\Add $subAdd
    ) {
        parent::__construct($logger, $dba, $toolbox, $callRepo);
        $this->_callTypeOperation = $callTypeOperation;
        $this->_repoMod = $repoMod;
        $this->_subAdd = $subAdd;
    }

    /**
     * Add operation with list of transactions and change account balances.
     *
     * @param Request\Add $req
     *
     * @return Response\Add
     */
    public function add(Request\Add $req) {
        $result = new Response\Add();
        $operationTypeId = $req->getOperationTypeId();
        $operationTypeCode = $req->getOperationTypeCode();
        $datePerformed = $req->getDatePerformed();
        $transactions = $req->getTransactions();
        $asRef = $req->getAsTransRef();
        $conn = $this->_getConn();
        $conn->beginTransaction();
        try {
            /* add operation itself */
            if(!$operationTypeId) {
                $operationTypeId = $this->_repoMod->getTypeOperationIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                Operation::ATTR_TYPE_ID        => $operationTypeId,
                Operation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            $reqAdd = new  AddEntityRequest(Operation::ENTITY_NAME, $bindToAdd);
            /** @var  $respAdd AddEntityResponse */
            $respAdd = $this->_callRepo->addEntity($reqAdd);
            if($respAdd->isSucceed()) {
                $operId = $respAdd->getIdInserted();
                $transIds = $this->_subAdd->transactions($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
                $conn->commit();
                $result->setAsSucceed();
            }
        } finally {
            if(!$result->isSucceed()) {
                $conn->rollback();
            }
        }
        return $result;
    }
}