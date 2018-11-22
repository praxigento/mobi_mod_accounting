<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2017
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Response as AResponse;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Data\Transaction as ETran;
use Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMaxDateBalance as QGetMaxDate;
use Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMinDateTrans as QGetMinDate;
use Praxigento\Core\Api\Helper\Period as HPeriod;

class LastDate
    implements \Praxigento\Accounting\Api\Service\Account\Balance\LastDate
{
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMaxDateBalance */
    private $qGetMaxDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMinDateTrans */
    private $qGetMinDate;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMaxDateBalance $qGetMaxDate,
        \Praxigento\Accounting\Service\Account\Balance\LastDate\A\Repo\Query\GetMinDateTrans $qGetMinDate
    ) {
        $this->daoTypeAsset = $daoTypeAsset;
        $this->hlpPeriod = $hlpPeriod;
        $this->qGetMaxDate = $qGetMaxDate;
        $this->qGetMinDate = $qGetMinDate;
    }

    /**
     * Get the last date for the balance (by asset type or by account).
     *
     * @param \Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();

        /** define local working data */
        $accountId = $request->getAccountId();
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $lastDate = null;

        /** validate pre-processing conditions */
        if (
            is_null($assetTypeId) &&
            !empty($assetTypeCode)
        ) {
            $assetTypeId = $this->daoTypeAsset->getIdByCode($assetTypeCode);
        }

        /** perform processing */
        if (!empty($accountId)) {
            /* prefer to get by account */
            $lastDate = $this->getBalanceMaxDateByAccId($accountId);
            if (!$lastDate) {
                /* balances are empty for the account, get transaction with minimal date */
                $transMinDate = $this->getTranMinDateByAccId($accountId);
                if ($transMinDate) {
                    $period = $this->hlpPeriod->getPeriodCurrent($transMinDate);
                    $lastDate = $this->hlpPeriod->getPeriodPrev($period, HPeriod::TYPE_DAY);
                }
            }
        } else if (!empty($assetTypeId)) {
            /* or get by assetType */
            $lastDate = $this->getBalanceMaxDateByAssetType($assetTypeId);
            if (!$lastDate) {
                /* there is no balance data yet, get transaction with minimal date */
                $transMinDate = $this->getTranMinDateByAssetType($assetTypeId);
                if ($transMinDate) {
                    $period = $this->hlpPeriod->getPeriodCurrent($transMinDate);
                    $lastDate = $this->hlpPeriod->getPeriodPrev($period, HPeriod::TYPE_DAY);
                }
            }
        }

        /** compose result */
        if ($lastDate) {
            $result->setLastDate($lastDate);
            $result->markSucceed();
        }
        return $result;
    }

    private function getBalanceMaxDateByAccId($accId)
    {
        $query = $this->qGetMaxDate->build();
        $where = QGetMaxDate::AS_BAL . '.' . EBalance::A_ACCOUNT_ID . '=' . (int)$accId;
        $query->where($where);

        $conn = $query->getConnection();
        $result = $conn->fetchOne($query);
        return $result;
    }

    private function getBalanceMaxDateByAssetType($assetTypeId)
    {
        $query = $this->qGetMaxDate->build();
        $where = QGetMaxDate::AS_ACC . '.' . EAccount::A_ASSET_TYPE_ID . '=' . (int)$assetTypeId;
        $query->where($where);

        $conn = $query->getConnection();
        $result = $conn->fetchOne($query);
        return $result;
    }

    private function getTranMinDateByAccId($accId)
    {
        $query = $this->qGetMinDate->build();
        $byDebit = QGetMinDate::AS_TRAN . '.' . ETran::A_DEBIT_ACC_ID . '=' . (int)$accId;
        $byCredit = QGetMinDate::AS_TRAN . '.' . ETran::A_CREDIT_ACC_ID . '=' . (int)$accId;
        $where = "($byDebit) OR ($byCredit)";
        $query->where($where);

        $conn = $query->getConnection();
        $result = $conn->fetchOne($query);
        return $result;
    }

    private function getTranMinDateByAssetType($assetTypeId)
    {
        $query = $this->qGetMinDate->build();
        $where = QGetMinDate::AS_ACC . '.' . EAccount::A_ASSET_TYPE_ID . '=' . (int)$assetTypeId;
        $query->where($where);

        $conn = $query->getConnection();
        $result = $conn->fetchOne($query);
        return $result;
    }
}