<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Operation\Create\A;

use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Accounting\Service\Transaction\Create\Request as ARequest;
use Praxigento\Accounting\Service\Transaction\Create\Response as AResponse;

class Add
{
    /**
     * @var \Praxigento\Accounting\Service\Transaction\Create
     */
    private $srvTrans;

    public function __construct(
        \Praxigento\Accounting\Service\Transaction\Create $srvTrans
    ) {
        $this->srvTrans = $srvTrans;
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
    public function exec($operId, $trans, $datePerformed, $asRef = null)
    {
        $result = [];
        foreach ($trans as $one) {
            if (!$one instanceof ETrans) {
                $one = new ETrans($one);
            }
            $dateApplied = $one->getDateApplied();
            $dateApplied = $dateApplied ? $dateApplied : $datePerformed;
            $req = new ARequest();
            $req->setOperationId($operId);
            $req->setDebitAccId($one->getDebitAccId());
            $req->setCreditAccId($one->getCreditAccId());
            $req->setValue($one->getValue());
            $req->setNote($one->getNote());
            $req->setDateApplied($dateApplied);
            /** @var  $resp AResponse */
            $resp = $this->srvTrans->exec($req);
            if (!$resp->isSucceed()) {
                throw new \Exception("Transaction (debit acc. #{$req->getDebitAccId()}, credit acc. "
                    . "#{$req->getCreditAccId()}) cannot be inserted . ");
            }
            $tranId = $resp->getTransactionId();
            $ref = $one->get($asRef);
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