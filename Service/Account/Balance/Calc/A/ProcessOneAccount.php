<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A;

use Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing as QBalOnDate;
use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ALastDateRequest;
use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as AResetRequest;

/**
 * Re-calculate balances for given asset type.
 */
class ProcessOneAccount
{
    private const BND_ACC_ID = 'accId';

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTransaction;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing */
    private $qBalancesOnDate;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Reset */
    private $servBalanceReset;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\CollectTransactions */
    private $zCollect;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing $qBalancesOnDate,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Reset $servBalanceReset,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\Z\CollectTransactions $zCollect
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->daoAccount = $daoAccount;
        $this->daoBalance = $daoBalance;
        $this->daoTransaction = $daoTransaction;
        $this->qBalancesOnDate = $qBalancesOnDate;
        $this->hlpDate = $hlpDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->servBalanceLastDate = $servBalanceLastDate;
        $this->servBalanceReset = $servBalanceReset;
        $this->zCollect = $zCollect;
    }

    public function exec($accId, $dsBalClose)
    {
        $dsLast = $this->getBalancesLastDate($accId);
        /* reset balances if requested */
        if ($dsLast > $dsBalClose) {
            $this->resetBalances($accId, $dsBalClose);
            $dsLast = $dsBalClose;
        }
        /* get closing balances */
        $balance = $this->getBalanceClosing($accId, $dsLast);
        /* get transactions starting from the last date */
        $trans = $this->getTransactions($accId, $dsLast);
        if (is_array($trans) && count($trans)) {
            /* collect transactions by date (compose records for balances table) */
            $balances = [$accId => $balance];
            $updates = $this->zCollect->exec($balances, $trans);
            /* process this account balances only*/
            $updates = [$accId => $updates[$accId]];
            /* TODO extract saveUpdates to the Z-class */
            $this->saveUpdates($updates);
        } else {
            $msg = "There is no transactions for account #$accId starting from $dsLast";
            $this->logger->info($msg);
        }
    }


    private function getBalanceClosing($accId, $dsClose)
    {
        $result = 0;

        $query = $this->qBalancesOnDate->build();
        $conn = $query->getConnection();

        $where = QBalOnDate::AS_ACC . '.' . EAccount::A_ID . '=:' . self::BND_ACC_ID;
        $query->where($where);

        $bind = [
            QBalOnDate::BND_MAX_DATE => $dsClose,
            self::BND_ACC_ID => $accId
        ];
        $rs = $conn->fetchAll($query, $bind);
        if (is_array($rs)) {
            $entry = reset($rs);
            $result = $entry[QBalOnDate::A_BALANCE];
        }
        return $result;
    }

    /**
     * Get the last datestamp for existing balances for given asset.
     *
     * @param int $accId
     * @return string
     * @throws \Exception
     */
    private function getBalancesLastDate($accId)
    {
        $reqLastDate = new ALastDateRequest();
        $reqLastDate->setAccountId($accId);
        $respLastDate = $this->servBalanceLastDate->exec($reqLastDate);
        $result = $respLastDate->getLastDate();
        return $result;
    }

    private function getTransactions($accId, $dsClose)
    {
        /* first date should be after closing balance date */
        $tsFrom = $this->hlpPeriod->getTimestampNextFrom($dsClose);
        $conn = $this->resource->getConnection();
        $byDateFrom = ETrans::A_DATE_APPLIED . '>=' . $conn->quote($tsFrom);
        $byAccIdDebit = ETrans::A_DEBIT_ACC_ID . '=' . (int)$accId;
        $byAccIdCredit = ETrans::A_CREDIT_ACC_ID . '=' . (int)$accId;
        $where = "($byDateFrom) AND (($byAccIdDebit) OR ($byAccIdCredit))";
        $order = ETrans::A_DATE_APPLIED . ' ASC';
        $result = $this->daoTransaction->get($where, $order);
        return $result;
    }

    /**
     * @param int $accId
     * @param string $dsFrom
     * @throws \Exception
     */
    private function resetBalances($accId, $dsFrom)
    {
        $req = new AResetRequest();
        $req->setAccounts([$accId]);
        $req->setDateFrom($dsFrom);
        $this->servBalanceReset->exec($req);
    }

    private function saveUpdates($updates)
    {
        $accBalances = [];
        /* save daily balances and collect actual balance for accounts */
        foreach ($updates as $ds => $entries) {
            /** @var EBalance $entry */
            foreach ($entries as $entry) {
                $accId = $entry->getAccountId();
                $balanceClose = $entry->getBalanceClose();
                $this->daoBalance->create($entry);
                $accBalances[$accId] = $balanceClose;
            }
        }
        /* update account balances */
        foreach ($accBalances as $accId => $balance) {
            $account = $this->daoAccount->getById($accId);
            $current = $account->getBalance();
            if (abs($current - $balance) > Cfg::DEF_ZERO) {
                $msg = "Wrong current balance for account #$accId (act.: $current; exp.: $balance) is fixed.";
                $this->logger->warning($msg);
                $account->setBalance($balance);
                $this->daoAccount->updateById($accId, $account);
            }
        }
    }
}