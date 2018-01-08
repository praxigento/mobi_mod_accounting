<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\LastDate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\LastDate\Response as AResponse;
use Praxigento\Core\Api\Helper\Period as HPeriod;


class LastDate
{
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    private $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    private $repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod
    ) {
        $this->repoBalance = $repoBalance;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeAsset = $repoTypeAsset;
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
            $assetTypeId = $this->repoTypeAsset->getIdByCode($assetTypeCode);
        }
        /* get the maximal date for balance */
        $balanceMaxDate = $this->repoBalance->getMaxDate($assetTypeId);
        if ($balanceMaxDate) {
            /* there is balance data */
            //$dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, HPeriod::TYPE_DAY);
            $result->set([AResponse::LAST_DATE => $balanceMaxDate]);
            $result->markSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->repoTransaction->getMinDateApplied($assetTypeId);
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