<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Accounting\Service\Account\Balance\Validate\A;

use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ARequestLastDate;
use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Response as AResponseLastDate;
use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset as QBalanceOnDate;
use Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Data\Trans as DTrans;
use Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Repo\Query\GetTransactions as QGetTrans;

class ProcessOneAsset
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTrans;
    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset */
    private $qBalanceOnDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Repo\Query\GetTransactions */
    private $qGetTrans;
    /** @var \Praxigento\Accounting\Api\Service\Account\Balance\LastDate */
    private $servLastDate;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTrans,
        \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset $qBalanceOnDate,
        \Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Repo\Query\GetTransactions $qGetTrans,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Api\Service\Account\Balance\LastDate $servLastDate
    ) {
        $this->logger = $logger;
        $this->daoAcc = $daoAcc;
        $this->daoTrans = $daoTrans;
        $this->qBalanceOnDate = $qBalanceOnDate;
        $this->qGetTrans = $qGetTrans;
        $this->hlpPeriod = $hlpPeriod;
        $this->servLastDate = $servLastDate;
    }

    /**
     * Validate balances for one asset type.
     *
     * @param int $assetTypeId
     * @throws \Exception
     */
    public function exec($assetTypeId)
    {
        /* get the last date with balances for given asset then take previous date */
        $dsLast = $this->getDateBalanceClose($assetTypeId);
        $dsPrev = $this->hlpPeriod->getPeriodPrev($dsLast);
        /* get all closing balances for previous date */
        $balances = $this->getBalances($assetTypeId, $dsPrev);
        /* get all accounts with given asset type */
        $accounts = $this->getAccounts($assetTypeId);
        /* get all transactions starting from "balance close -1 day" for given asset */
        $trans = $this->getTransactions($assetTypeId, $dsPrev);
        /* then validate current balances */
        $this->validate($accounts, $balances, $trans);
    }

    /**
     * Get current balances for account with given asset ($typeId).
     *
     * @param int $typeId
     * @return array [$accountId => $balance]
     */
    private function getAccounts($typeId)
    {
        $result = [];
        $where = EAccount::A_ASSET_TYPE_ID . '=' . (int)$typeId;
        $rows = $this->daoAcc->get($where);
        /** @var EAccount $row */
        foreach ($rows as $row) {
            $accId = $row->getId();
            $balance = $row->getBalance();
            $result[$accId] = $balance;
        }
        return $result;
    }

    /**
     * Get closing balances on given date ($ds) for given asset ($typeId).
     *
     * @param int $typeId
     * @param string $ds
     * @return array [$accountId => $balance]
     */
    private function getBalances($typeId, $ds)
    {
        $result = [];
        $query = $this->qBalanceOnDate->build();
        $conn = $query->getConnection();
        $bind = [
            QBalanceOnDate::BND_ASSET_TYPE_ID => $typeId,
            QBalanceOnDate::BND_MAX_DATE => $ds
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $accId = $row[QBalanceOnDate::A_ACC_ID];
            $balance = $row[QBalanceOnDate::A_BALANCE];
            $result[$accId] = $balance;
        }
        return $result;
    }

    /**
     * Get datestamp for the last day of available balances.
     *
     * @param int $typeId asset type
     * @return string YYYYMMDD
     * @throws \Exception
     */
    private function getDateBalanceClose($typeId)
    {
        $req = new ARequestLastDate();
        $req->setAssetTypeId($typeId);
        /** @var AResponseLastDate $resp */
        $resp = $this->servLastDate->exec($req);
        $result = $resp->getLastDate();
        return $result;
    }

    /**
     * @param int $typeId
     * @param string $ds 'YYYYMMDD'
     * @return DTrans[]
     */
    private function getTransactions($typeId, $ds)
    {
        $result = [];
        $dateFrom = $this->hlpPeriod->getTimestampNextFrom($ds);
        $query = $this->qGetTrans->build();
        $conn = $query->getConnection();
        $bind = [
            QGetTrans::BND_ASSET_TYPE_ID => $typeId,
            QGetTrans::BND_DATE_FROM => $dateFrom
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $item = new DTrans();
            $item->accIdDebit = $row[QGetTrans::A_ACC_ID_DEBIT];
            $item->accIdCredit = $row[QGetTrans::A_ACC_ID_CREDIT];
            $item->amount = $row[QGetTrans::A_AMOUNT];
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param array $accounts
     * @param array $balances
     * @param DTrans[] $trans
     */
    private function validate($accounts, $balances, $trans)
    {
        /* compose current balances based on $balances & $transactions */
        foreach ($trans as $one) {
            $accIdDebit = $one->accIdDebit;
            $accIdCredit = $one->accIdCredit;
            $amount = $one->amount;
            if (isset($balances[$accIdDebit])) {
                $balances[$accIdDebit] -= $amount;
            } else {
                $balances[$accIdDebit] = (-1) * $amount;
            }
            if (isset($balances[$accIdCredit])) {
                $balances[$accIdCredit] += $amount;
            } else {
                $balances[$accIdCredit] = $amount;
            }
        }
        /* total sum of all balances should be equal to zero.*/
        $sum = 0;
        foreach ($accounts as $accId => $balanceAct) {
            /* accounts with zero balances are not presented in balance array */
            if (abs($balanceAct) < Cfg::DEF_ZERO)
                continue;
            $sum += $balanceAct;
            $balanceExp = $balances[$accId];
            if (abs($balanceAct - $balanceExp) > Cfg::DEF_ZERO) {
                $this->logger->error("Account balance validation error: acc. #$accId, actual: $balanceAct, expected: $balanceExp.");
            }
        }
        if (abs($sum) > Cfg::DEF_ZERO) {
            $this->logger->error("Total summary for accounts balances is not equal to 0 (actual: $sum).");
        }
    }
}