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

    /** @var \Praxigento\Core\Tool\IDate */
    private $hlpDate;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    private $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    private $repoTransaction;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\Simple Simple balance calculator. */
    private $servCalcSimple;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Core\Tool\IDate $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Calc\Simple $servCalcSimple
    ) {
        $this->repoBalance = $repoBalance;
        $this->repoTransaction = $repoTransaction;
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
        $balances = $this->repoBalance->getOnDate($assetTypeId, $lastDate);
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
            $trans = $this->repoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
            $updates = $this->servCalcSimple->exec($balances, $trans);
            $this->repoBalance->updateBalances($updates);
            $result->markSucceed();
        }
        return $result;
    }

}