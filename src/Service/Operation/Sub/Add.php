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
            if (!$one instanceof Transaction) {
                $one = new Transaction($one);
            }
            $dateApplied = $one->getDateApplied();
            $dateApplied = $dateApplied ? $dateApplied : $datePerformed;
            $req = new AddTransactionRequest();
            $req->setOperationId($operId);
            $req->setDebitAccId($one->getDebitAccId());
            $req->setCreditAccId($one->getCreditAccId());
            $req->setValue($one->getValue());
            $req->setDateApplied($dateApplied);
            /** @var  $resp AddTransactionResponse */
            $resp = $this->_callTransaction->add($req);
            if (!$resp->isSucceed()) {
                throw new \Exception("Transaction (debit acc. #{$req->getDebitAccId()}, credit acc. "
                    . "#{$req->getCreditAccId()}) cannot be inserted . ");
            }
            $tranId = $resp->getTransactionId();
            $ref = $one->getData($asRef);
            if (
                !is_null($asRef) &&
                isset($ref)
            ) {
                /* bind new transaction ID to the reference from request */
                $result[$tranId] = $ref;
            } else {
                /* new transaction ID is bound by 'add transaction' requests order */
                $result[] = $resp->getTransactionId();
            }
        }
        return $result;
    }
}