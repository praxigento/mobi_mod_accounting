<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2017
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\LastDate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\LastDate\Response as AResponse;
use Praxigento\Core\Api\Helper\Period as HPeriod;


class LastDate
{
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTransaction;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod
    ) {
        $this->daoBalance = $daoBalance;
        $this->daoTransaction = $daoTransaction;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->hlpPeriod = $hlpPeriod;
    }

    /**
     * Get Last Date
     * Calculate the last date for the balance of the asset.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        if (is_null($assetTypeId)) {
            $assetTypeId = $this->daoTypeAsset->getIdByCode($assetTypeCode);
        }
        /* get the maximal date for balance */
        $balanceMaxDate = $this->daoBalance->getMaxDate($assetTypeId);
        if ($balanceMaxDate) {
            /* there is balance data */
            //$dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, HPeriod::TYPE_DAY);
            $result->set([AResponse::LAST_DATE => $balanceMaxDate]);
            $result->markSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->daoTransaction->getMinDateApplied($assetTypeId);
            if ($transactionMinDate) {
                $period = $this->hlpPeriod->getPeriodCurrent($transactionMinDate);
                $dayBefore = $this->hlpPeriod->getPeriodPrev($period, HPeriod::TYPE_DAY);
                $result->set([AResponse::LAST_DATE => $dayBefore]);
                $result->markSucceed();
            }
        }
        return $result;
    }
}