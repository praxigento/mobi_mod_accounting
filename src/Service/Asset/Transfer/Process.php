<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Asset\Transfer;

use Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Request as ARequest;
use Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Response as AResponse;
use Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Response\Data as ARespData;
use Praxigento\Accounting\Config as Cfg;

class Process
    implements \Praxigento\Accounting\Api\Ctrl\Asset\Transfer\ProcessInterface
{
    /** @var \Praxigento\Accounting\Service\IOperation */
    private $callOper;
    /** @var \Praxigento\Core\Tool\IDate */
    private $hlpData;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAcc;

    public function __construct(
        \Praxigento\Core\Tool\IDate $hlpData,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Service\IOperation $callOper
    )
    {
        $this->hlpData = $hlpData;
        $this->repoAcc = $repoAcc;
        $this->callOper = $callOper;
    }

    public function exec(ARequest $request)
    {
        /* define local working data */
        $amount = $request->getAmount();
        $assetTypeId = $request->getAssetId();
        $counterPartyId = $request->getCounterPartyId();
        $customerId = $request->getCustomerId();
        $isDirect = $request->getIsDirect();
        $userId = $request->getUserId();

        /* perform processing */
        /* find accounts */
        $accCust = $this->repoAcc->getByCustomerId($customerId, $assetTypeId);
        $accIdCust = $accCust->getId();
        if ($isDirect) {
            $accIdParty = $this->repoAcc->getRepresentativeAccountId($assetTypeId);
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
        $trans = $this->prepareTrans($amount, $accIdDebit, $accIdCredit);
        $data = $this->transfer($userId, $trans);

        /* compose result */
        $result = new AResponse();
        $result->setData($data);
        return $result;
    }

    private function prepareTrans($amount, $accIdDebit, $accIdCredit)
    {
        $tran = new \Praxigento\Accounting\Repo\Entity\Data\Transaction();
        $amountAbs = abs($amount);
        $tran->setDebitAccId($accIdDebit);
        $tran->setCreditAccId($accIdCredit);
        $tran->setValue($amountAbs);
        $dateApplied = $this->hlpData->getUtcNowForDb();
        $tran->setDateApplied($dateApplied);
        $result[] = $tran;
        return $result;
    }

    private function transfer($userId, $trans)
    {
        $req = new \Praxigento\Accounting\Service\Operation\Request\Add();
        $req->setAdminUserId($userId);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
        $req->setOperationNote("Asset transfer initiated by manager #$userId");
        $req->setTransactions($trans);
        $datePerformed = $this->hlpData->getUtcNowForDb();
        $req->setDatePerformed($datePerformed);
        $resp = $this->callOper->add($req);
        $operId = $resp->getOperationId();
        $result = new ARespData();
        $result->setOperId($operId);
        return $result;
    }
}