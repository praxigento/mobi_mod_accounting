<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset;

use Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Response as AResponse;
use Praxigento\Accounting\Config as Cfg;

/**
 * Service to process asset transfer between accounts (customer or system) initiated by customer itself or by backend
 * operator. All validations and restrictions should be performed before this service call (WebAPI or controller level).
 */
class Transfer
    implements \Praxigento\Accounting\Api\Service\Account\Asset\Transfer
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpData;
    /** @var \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\Helper\Date $hlpData,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Api\Service\Operation\Create $callOper
    ) {
        $this->hlpData = $hlpData;
        $this->daoAcc = $daoAcc;
        $this->servOper = $callOper;
    }

    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /* define local working data */
        $amount = $request->getAmount();
        $assetTypeId = $request->getAssetId();
        $counterPartyId = $request->getCounterPartyId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $isDirect = $request->getIsDirect();
        $note = $request->getNote();
        $userId = $request->getUserId();

        /* perform processing */
        /* find accounts */
        $accCust = $this->daoAcc->getByCustomerId($customerId, $assetTypeId);
        $accIdCust = $accCust->getId();
        if ($isDirect) {
            $accIdParty = $this->daoAcc->getSystemAccountId($assetTypeId);
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
            $accParty = $this->daoAcc->getByCustomerId($counterPartyId, $assetTypeId);
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
        $trans = $this->prepareTrans($amount, $accIdDebit, $accIdCredit, $dateApplied, $note);
        list($err, $operId) = $this->transfer($userId, $trans, $note);

        /* compose result */
        $result = new AResponse();
        $one = reset($trans);
        $amount = $one->getValue();
        $result->setAmount($amount);
        if ($err == AResponse::ERR_NO_ERROR) {
            $result->setOperId($operId);
            $result->markSucceed();
        } else {
            $result->setErrorCode($err);
            $result->setErrorMessage($err);
        }
        return $result;
    }

    /**
     * @param $amount
     * @param $accIdDebit
     * @param $accIdCredit
     * @param $dateApplied
     * @param $note
     * @return \Praxigento\Accounting\Repo\Data\Transaction[]
     * @throws \Exception
     */
    private function prepareTrans($amount, $accIdDebit, $accIdCredit, $dateApplied, $note)
    {
        $tran = new \Praxigento\Accounting\Repo\Data\Transaction();
        $amountAbs = abs($amount);
        $tran->setDebitAccId($accIdDebit);
        $tran->setCreditAccId($accIdCredit);
        $tran->setValue($amountAbs);
        $tran->setNote($note);
        if(!$dateApplied) $dateApplied = $this->hlpData->getUtcNowForDb();
        $tran->setDateApplied($dateApplied);
        $result[] = $tran;
        return $result;
    }

    private function transfer($userId, $trans, $note)
    {
        $req = new \Praxigento\Accounting\Api\Service\Operation\Create\Request();
        $req->setAdminUserId($userId);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
        if (!$note) {
            /* add default operation note */
            if ($userId) {
                $note = "Asset transfer initiated by manager #$userId.";
            } else {
                $note = "Asset transfer initiated by customer.";
            }
        }
        $req->setTransactions($trans);
        $req->setOperationNote($note);
        $datePerformed = $this->hlpData->getUtcNowForDb();
        $req->setDatePerformed($datePerformed);
        $resp = $this->servOper->exec($req);
        $err = $resp->getErrorCode();
        $operId = $resp->getOperationId();
        return [$err, $operId];
    }
}