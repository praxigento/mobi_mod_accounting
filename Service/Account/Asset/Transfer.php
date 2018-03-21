<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Service\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Accounting\Service\Account\Asset\Transfer\Response as AResponse;

/**
 * Internal service to process asset transfer between accounts (customer or system).
 *
 * This service is not used outside this module.
 */
class Transfer
{
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $callOper;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpData;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAcc;

    public function __construct(
        \Praxigento\Core\Api\Helper\Date $hlpData,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Api\Service\Operation $callOper
    ) {
        $this->hlpData = $hlpData;
        $this->repoAcc = $repoAcc;
        $this->callOper = $callOper;
    }

    /**
     * @param \Praxigento\Accounting\Service\Account\Asset\Transfer\Request $request
     * @return \Praxigento\Accounting\Service\Account\Asset\Transfer\Response
     * @throws \Exception
     */
    public function exec(ARequest $request)
    {
        assert($request instanceof ARequest);
        /* define local working data */
        $amount = $request->getAmount();
        $assetTypeId = $request->getAssetId();
        $counterPartyId = $request->getCounterPartyId();
        $customerId = $request->getCustomerId();
        $isDirect = $request->getIsDirect();
        $note = $request->getNote();
        $userId = $request->getUserId();

        /* perform processing */
        /* find accounts */
        $accCust = $this->repoAcc->getByCustomerId($customerId, $assetTypeId);
        $accIdCust = $accCust->getId();
        if ($isDirect) {
            $accIdParty = $this->repoAcc->getSystemAccountId($assetTypeId);
            /* define transfer direction */
            if ($amount > 0) {
                /* add funds to customer account */
                $accIdDebit = $accIdParty;
                $accIdCredit = $accIdCust;
            } else {
                /* deduct funds from customer account */
                $accIdDebit = $accIdCust;
                $accIdCredit = $accIdParty;
            }
        } else {
            $accParty = $this->repoAcc->getByCustomerId($counterPartyId, $assetTypeId);
            $accIdParty = $accParty->getId();
            if ($amount > 0) {
                /* move funds from customer account to party account */
                $accIdDebit = $accIdCust;
                $accIdCredit = $accIdParty;
            } else {
                /* move funds from party account to customer account */
                $accIdDebit = $accIdParty;
                $accIdCredit = $accIdCust;
            }
        }
        /* compose transaction and create operation */
        $trans = $this->prepareTrans($amount, $accIdDebit, $accIdCredit, $note);
        $operId = $this->transfer($userId, $trans, $note);

        /* compose result */
        $result = new AResponse();
        $result->setOperId($operId);
        return $result;
    }

    private function prepareTrans($amount, $accIdDebit, $accIdCredit, $note)
    {
        $tran = new \Praxigento\Accounting\Repo\Entity\Data\Transaction();
        $amountAbs = abs($amount);
        $tran->setDebitAccId($accIdDebit);
        $tran->setCreditAccId($accIdCredit);
        $tran->setValue($amountAbs);
        $tran->setNote($note);
        $dateApplied = $this->hlpData->getUtcNowForDb();
        $tran->setDateApplied($dateApplied);
        $result[] = $tran;
        return $result;
    }

    private function transfer($userId, $trans, $note)
    {
        $req = new \Praxigento\Accounting\Api\Service\Operation\Request();
        $req->setAdminUserId($userId);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
        if ($userId) {
            $req->setOperationNote("Asset transfer initiated by manager #$userId.");
        } else {
            $req->setOperationNote("Asset transfer initiated by customer.");
        }
        $req->setTransactions($trans);
        $req->setOperationNote($note);
        $datePerformed = $this->hlpData->getUtcNowForDb();
        $req->setDatePerformed($datePerformed);
        $resp = $this->callOper->exec($req);
        $result = $resp->getOperationId();
        return $result;
    }
}