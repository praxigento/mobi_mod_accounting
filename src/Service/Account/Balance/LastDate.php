<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Service\Account\Balance\LastDate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\LastDate\Response as AResponse;
use Praxigento\Core\Tool\IPeriod;


class LastDate
{
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    protected $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $repoTransaction;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    protected $repoTypeAsset;
    /** @var  \Praxigento\Core\Tool\IPeriod */
    protected $toolPeriod;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTransaction,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Core\Tool\IPeriod $toolPeriod
    )
    {
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoTransaction = $repoTransaction;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->toolPeriod = $toolPeriod;
    }

    /**
     * Get Last Date
     * Calculate the last date for the balance of the asset.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
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
            //$dayBefore = $this->_toolPeriod->getPeriodPrev($balanceMaxDate, IPeriod::TYPE_DAY);
            $result->set([AResponse::LAST_DATE => $balanceMaxDate]);
            $result->markSucceed();
        } else {
            /* there is no balance data yet, get transaction with minimal date */
            $transactionMinDate = $this->repoTransaction->getMinDateApplied($assetTypeId);
            if ($transactionMinDate) {
                $period = $this->toolPeriod->getPeriodCurrentOld($transactionMinDate);
                $dayBefore = $this->toolPeriod->getPeriodPrev($period, IPeriod::TYPE_DAY);
                $result->set([AResponse::LAST_DATE => $dayBefore]);
                $result->markSucceed();
            }
        }
        return $result;
    }
}