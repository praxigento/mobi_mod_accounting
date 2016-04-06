<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Service\Balance;

use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Lib\Service\IBalance;
use Praxigento\Core\Tool\IPeriod;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements IBalance
{
    /**
     * @var \Praxigento\Accounting\Lib\Repo\IModule
     */
    protected $_repoMod;
    /** @var Sub\CalcSimple Simple balance calculator. */
    protected $_subCalcSimple;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $_toolPeriod;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Tool\IPeriod $toolPeriod,
        \Praxigento\Accounting\Lib\Repo\IModule $repoMod,
        Sub\CalcSimple $subCalcSimple
    ) {
        parent::__construct($logger);
        $this->_toolPeriod = $toolPeriod;
        $this->_repoMod = $repoMod;
        $this->_subCalcSimple = $subCalcSimple;
    }

    /**
     * Calculate asset balances up to given date (including).
     *
     * @param Request\Calc $request
     *
     * @return Response\Calc
     */
    public function calc(Request\Calc $request)
    {
        $result = new Response\Calc();
        $assetTypeId = $request->getData(Request\Calc::ASSET_TYPE_ID);
        $dateTo = $request->getData(Request\Calc::DATE_TO);
        /* get the last balance date */
        $reqLastDate = new Request\GetLastDate();
        $reqLastDate->setData(Request\GetLastDate::ASSET_TYPE_ID, $assetTypeId);
        $respLastDate = $this->getLastDate($reqLastDate);
        $lastDate = $respLastDate->getLastDate();
        $balances = $this->_repoMod->getBalancesOnDate($assetTypeId, $lastDate);
        /* get transactions for period */
        $dtFrom = $this->_toolPeriod->getTimestampFrom($lastDate, IPeriod::TYPE_DAY);
        $dtTo = $this->_toolPeriod->getTimestampTo($dateTo, IPeriod::TYPE_DAY);
        $trans = $this->_repoMod->getTransactionsForPeriod($assetTypeId, $dtFrom, $dtTo);
        $updates = $this->_subCalcSimple->calcBalances($balances, $trans);
        $this->_repoMod->updateBalances($updates);
        $result->setErrorCode(Response\Calc::ERR_NO_ERROR);
        return $result;
    }

    /**
     * Get asset balances on the requested date.
     *
     * @param Request\GetBalancesOnDate $request
     *
     * @return Response\GetBalancesOnDate
     */
    public function getBalancesOnDate(Request\GetBalancesOnDate $request)
    {
        $result = new Response\GetBalancesOnDate();
        $dateOn = $request->getData(Request\GetBalancesOnDate::DATE);
        $assetTypeId = $request->getData(Request\GetBalancesOnDate::ASSET_TYPE_ID);
        $rows = $this->_repoMod->getBalancesOnDate($assetTypeId, $dateOn);
        if (count($rows) > 0) {
            $result->setData($rows);
            $result->setAsSucceed();
        }
        return $result;
    }

    /**
     * Calculate the last date for the balance of the asset.
     *
     * @param Request\GetLastDate $request
     *
     * @return Response\GetLastDate
     */
    public function getLastDate(Request\GetLastDate $request)
    {
        $result = new Response\GetLastDate();
        $assetTypeId = $request->getData(Request\GetLastDate::ASSET_TYPE_ID);
        $assetTypeCode = $request->getData(Request\GetLastDate::ASSET_TYPE_CODE);
        if (is_null($assetTypeId)) {
            $assetTypeId = $this->_repoMod->getTypeAssetIdByCode($assetTypeCode);
        }
        /* get the maximal date for balance */
        $balanceMaxDate = $this->_repoMod->getBalanceMaxDate($assetTypeId);
        if ($balanceMaxDate) {
            /* there is balance data */
            $dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, IPeriod::TYPE_DAY);
            $result->setData([Response\GetLastDate::LAST_DATE => $dayBefore]);
            $result->setAsSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->_repoMod->getTransactionMinDateApplied($assetTypeId);
            if ($transactionMinDate) {
                $period = $this->_toolPeriod->getPeriodCurrent($transactionMinDate);
                $dayBefore = $this->_toolPeriod->getPeriodPrev($period, IPeriod::TYPE_DAY);
                $result->setData([Response\GetLastDate::LAST_DATE => $dayBefore]);
                $result->setAsSucceed();
            }
        }
        return $result;
    }

    /**
     * Reset balance history for all accounts on dates after requested.
     *
     * @param Request\Reset $request
     *
     * @return Response\Reset
     */
    public function reset(Request\Reset $request)
    {
        $result = new Response\Reset();
        $dateFrom = $request->getData(Request\Reset::DATE_FROM);
        /* get balance record with MAX date */
        $tbl = $this->_getTableName(Balance::ENTITY_NAME);
        $where = Balance::ATTR_DATE . '>=' . $this->_getConn()->quote($dateFrom);
        $rows = $this->_getConn()->delete($tbl, $where);
        if ($rows !== false) {
            $result->setData([Response\Reset::ROWS_DELETED => $rows]);
            $result->setAsSucceed();
        }
        return $result;
    }

}