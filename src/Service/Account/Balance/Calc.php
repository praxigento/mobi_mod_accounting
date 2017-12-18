<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Calc\Response as AResponse;

class Calc
{
    /** @var \Praxigento\Accounting\Service\Balance\Call */
    protected $balanceCall;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $repoTransaction;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\Simple Simple balance calculator. */
    protected $subCalcSimple;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $toolDate;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Service\Balance\Call $balanceCall,
        \Praxigento\Accounting\Service\Account\Balance\Calc\Simple $subCalcSimple
    )
    {
        $this->repoBalance = $repoBalance;
        $this->repoTransaction = $repoTransaction;
        $this->toolDate = $toolDate;
        $this->toolPeriod = $toolPeriod;
        $this->balanceCall = $balanceCall;
        $this->subCalcSimple = $subCalcSimple;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        $result = new AResponse();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $dateTo = $request->getDateTo();
        /* get the last balance date */
        $reqLastDate = new \Praxigento\Accounting\Service\Balance\Request\GetLastDate();
        $reqLastDate->setAssetTypeId($assetTypeId);
        $reqLastDate->setAssetTypeCode($assetTypeCode);
        $respLastDate = $this->balanceCall->getLastDate($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $this->repoBalance->getOnDate($assetTypeId, $lastDate);
        /* check date to */
        if (is_null($dateTo)) {
            /* use 'yesterday' */
            $dtMageNow = $this->toolDate->getMageNow();
            $today = $this->toolPeriod->getPeriodCurrent($dtMageNow);
            $dateTo = $this->toolPeriod->getPeriodPrev($today);
        }
        /* get transactions for period */
        if ($lastDate) {
            /* first date should be after balance last date */
            $dtFrom = $this->toolPeriod->getTimestampNextFrom($lastDate);
            $dtTo = $this->toolPeriod->getTimestampTo($dateTo);
            $trans = $this->repoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
            $updates = $this->subCalcSimple->exec($balances, $trans);
            $this->repoBalance->updateBalances($updates);
            $result->markSucceed();
        }
        return $result;
    }

}