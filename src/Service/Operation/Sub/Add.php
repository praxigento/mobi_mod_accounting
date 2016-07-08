<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Operation\Sub;

use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Service\Transaction\Request\Add as AddTransactionRequest;
use Praxigento\Accounting\Service\Transaction\Response\Add as AddTransactionResponse;

class Add
{
    /**
     * @var \Praxigento\Accounting\Service\ITransaction
     */
    protected $_callTransaction;

    public function __construct(
        \Praxigento\Accounting\Service\ITransaction $callTransaction
    ) {
        $this->_callTransaction = $callTransaction;
    }

    /**
     * Add operation's transaction and bind transactions ids to references (orders or customers ids).
     *
     * @param $operId
     * @param $trans
     * @param $datePerformed
     * @param $asRef
     *
     * @return array
     * @throws \Exception
     */
    public function transactions($operId, $trans, $datePerformed, $asRef = null)
    {
        $result = [];
        foreach ($trans as $one) {
            $dateApplied = isset($one[Transaction::ATTR_DATE_APPLIED]) ? $one[Transaction::ATTR_DATE_APPLIED] : $datePerformed;
            $req = new AddTransactionRequest();
            $req->setOperationId($operId);
            $req->setDebitAccId($one[Transaction::ATTR_DEBIT_ACC_ID]);
            $req->setCreditAccId($one[Transaction::ATTR_CREDIT_ACC_ID]);
            $req->setValue($one[Transaction::ATTR_VALUE]);
            $req->setDateApplied($dateApplied);
            /** @var  $resp AddTransactionResponse */
            $resp = $this->_callTransaction->add($req);
            if (!$resp->isSucceed()) {
                throw new \Exception("Transaction (debit acc. #{$req->getDebitAccId()}, credit acc. #{$req->getCreditAccId()}) cannot be inserted . ");
            }
            $tranId = $resp->getTransactionId();
            if (
                !is_null($asRef) &&
                isset($one[$asRef])
            ) {
                /* bind new transaction ID to the reference from request */
                $ref = $one[$asRef];
                $result[$tranId] = $ref;
            } else {
                /* new transaction ID is bound by 'add transaction' requests order */
                $result[] = $resp->getTransactionId();
            }
        }
        return $result;
    }
}