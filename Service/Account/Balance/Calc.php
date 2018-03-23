<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Calc\Response as AResponse;

/**
 * Calculate daily balances.
 *
 * This service is not used outside this module.
 */
class Calc
{

    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTransaction;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\Simple Simple balance calculator. */
    private $servCalcSimple;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Calc\Simple $servCalcSimple
    ) {
        $this->daoBalance = $daoBalance;
        $this->daoTransaction = $daoTransaction;
        $this->hlpDate = $hlpDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->servBalanceLastDate = $servBalanceLastDate;
        $this->servCalcSimple = $servCalcSimple;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $dateTo = $request->getDateTo();
        /* get the last balance date */
        $reqLastDate = new \Praxigento\Accounting\Service\Account\Balance\LastDate\Request();
        $reqLastDate->setAssetTypeId($assetTypeId);
        $reqLastDate->setAssetTypeCode($assetTypeCode);
        $respLastDate = $this->servBalanceLastDate->exec($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $this->daoBalance->getOnDate($assetTypeId, $lastDate);
        /* check date to */
        if (is_null($dateTo)) {
            /* use 'yesterday' */
            $dtMageNow = $this->hlpDate->getMageNow();
            $today = $this->hlpPeriod->getPeriodCurrent($dtMageNow);
            $dateTo = $this->hlpPeriod->getPeriodPrev($today);
        }
        /* get transactions for period */
        if ($lastDate) {
            /* first date should be after balance last date */
            $dtFrom = $this->hlpPeriod->getTimestampNextFrom($lastDate);
            $dtTo = $this->hlpPeriod->getTimestampTo($dateTo);
            $trans = $this->daoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
            $updates = $this->servCalcSimple->exec($balances, $trans);
            $this->daoBalance->updateBalances($updates);
            $result->markSucceed();
        }
        return $result;
    }

}