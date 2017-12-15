<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Balance\Calc\Response as AResponse;

class Calc
    implements \Praxigento\Accounting\Api\Service\Account\Balance\Calc
{

    /**
     * @param ARequest $req
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
        $balanceCall = new \Praxigento\Accounting\Service\Balance\Call();
        $respLastDate = $balanceCall->getLastDate($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $balanceCall->repoBalance->getOnDate($assetTypeId, $lastDate);
        /* check date to */
        if (is_null($dateTo)) {
            /* use 'yesterday' */
            $dtMageNow = $balanceCall->toolDate->getMageNow();
            $today = $balanceCall->toolPeriod->getPeriodCurrent($dtMageNow);
            $dateTo = $balanceCall->toolPeriod->getPeriodPrev($today);
        }
        /* get transactions for period */
        if ($lastDate) {
            /* first date should be after balance last date */
            $dtFrom = $balanceCall->toolPeriod->getTimestampNextFrom($lastDate);
            $dtTo = $balanceCall->toolPeriod->getTimestampTo($dateTo);
            $trans = $balanceCall->repoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
            $updates = $balanceCall->subCalcSimple->calcBalances($balances, $trans);
            $balanceCall->repoBalance->updateBalances($updates);
            $result->markSucceed();
        }
        return $result;
    }

}