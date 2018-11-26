<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A;

use Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing as QBalOnDate;
use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ALastDateRequest;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\Repo\Query\GetTransactions as QGetTrans;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as AResetRequest;

/**
 * Re-calculate balances for given asset type.
 */
class ProcessOneType
{
    private const BND_ASSET_TYPE_ID = 'assetTypeId';

    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing */
    private $qBalancesOnDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\Repo\Query\GetTransactions */
    private $qGetTrans;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Reset */
    private $servBalanceReset;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\CollectTransactions */
    private $zCollect;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\UpdateBalances */
    private $zUpdate;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing $qBalancesOnDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Reset $servBalanceReset,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\CollectTransactions $zCollect,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\UpdateBalances $zUpdate,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\Repo\Query\GetTransactions $qGetTrans
    ) {
        $this->logger = $logger;
        $this->qBalancesOnDate = $qBalancesOnDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->servBalanceLastDate = $servBalanceLastDate;
        $this->servBalanceReset = $servBalanceReset;
        $this->zCollect = $zCollect;
        $this->zUpdate = $zUpdate;
        $this->qGetTrans = $qGetTrans;
    }

    public function exec($assetTypeId, $dsBalClose)
    {
        $dsLast = $this->getBalancesLastDate($assetTypeId);
        /* reset balances if requested */
        if ($dsLast > $dsBalClose) {
            $this->resetBalances($assetTypeId, $dsBalClose);
            $dsLast = $dsBalClose;
        }
        /* get closing balances */
        $balances = $this->getBalanceClosing($assetTypeId, $dsLast);
        /* get transactions starting from the last date */
        $trans = $this->getTransactions($assetTypeId, $dsLast);
        if (is_array($trans) && count($trans)) {
            /* collect transactions by date (compose records for balances table) */
            $updates = $this->zCollect->exec($balances, $trans);
            $this->zUpdate->exec($updates);
        } else {
            $msg = "There is no transactions for asset type #$assetTypeId starting from $dsLast";
            $this->logger->info($msg);
        }
    }

    private function getBalanceClosing($assetTypeId, $dsClose)
    {
        $result = [];

        $query = $this->qBalancesOnDate->build();
        $conn = $query->getConnection();

        $where = QBalOnDate::AS_ACC . '.' . EAccount::A_ASSET_TYPE_ID . '=:' . self::BND_ASSET_TYPE_ID;
        $query->where($where);

        $bind = [
            QBalOnDate::BND_MAX_DATE => $dsClose,
            self::BND_ASSET_TYPE_ID => $assetTypeId
        ];
        $rs = $conn->fetchAll($query, $bind);
        if (is_array($rs)) {
            foreach ($rs as $one) {
                $accId = $one[QBalOnDate::A_ACC_ID];
                $balance = $one[QBalOnDate::A_BALANCE];
                $result [$accId] = $balance;
            }
        }
        return $result;
    }

    /**
     * Get the last datestamp for existing balances for given asset.
     *
     * @param int $assetTypeId
     * @return string
     * @throws \Exception
     */
    private function getBalancesLastDate($assetTypeId)
    {
        $reqLastDate = new ALastDateRequest();
        $reqLastDate->setAssetTypeId($assetTypeId);
        $respLastDate = $this->servBalanceLastDate->exec($reqLastDate);
        $result = $respLastDate->getLastDate();
        return $result;
    }

    /**
     * @param int $assetTypeId
     * @param string $dsClose YYYYMMDD (excl.)
     * @return ETrans[]
     * @throws \Exception
     */
    private function getTransactions($assetTypeId, $dsClose)
    {
        $result = [];
        /* first date should be after closing balance date */
        $tsFrom = $this->hlpPeriod->getTimestampNextFrom($dsClose);

        $query = $this->qGetTrans->build();
        $conn = $query->getConnection();
        $bind = [
            QGetTrans::BND_DATE_APPL => $tsFrom,
            QGetTrans::BND_ASSET_TYPE => $assetTypeId
        ];
        $rs = $conn->fetchAll($query, $bind);
        if ($rs) {
            foreach ($rs as $one) {
                $item = new ETrans($one);
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @param int $assetType
     * @param string $dsFrom
     * @throws \Exception
     */
    private function resetBalances($assetType, $dsFrom)
    {
        $req = new AResetRequest();
        $req->setAssetTypes([$assetType]);
        $req->setDateFrom($dsFrom);
        $this->servBalanceReset->exec($req);
    }

}