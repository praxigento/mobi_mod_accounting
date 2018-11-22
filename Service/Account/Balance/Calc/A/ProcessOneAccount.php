<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ALastDateRequest;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as AResetRequest;

/**
 * Re-calculate balances for given asset type.
 */
class ProcessOneAccount
{
    /** Max 'up to' datestamp to get transactions (TODO: increase value after 2999/12/31) */
    private const DATESTAMP_TO = '29991231';

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
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\CollectTransactions */
    private $ownCollect;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Reset */
    private $servBalanceReset;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Reset $servBalanceReset,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A\CollectTransactions $ownCollect
    ) {
        $this->logger = $logger;
        $this->daoAccount = $daoAccount;
        $this->daoBalance = $daoBalance;
        $this->daoTransaction = $daoTransaction;
        $this->hlpDate = $hlpDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->servBalanceLastDate = $servBalanceLastDate;
        $this->servBalanceReset = $servBalanceReset;
        $this->ownCollect = $ownCollect;
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
        $balances = $this->getBalanceClosing($accId, $dsLast);
        /* get transactions starting from the last date */
        $trans = $this->getTransactions($accId, $dsLast);
        /* collect transactions by date (compose records for balances table) */
        $updates = $this->ownCollect->exec($balances, $trans);
        $this->saveUpdates($updates);
    }

    private function getBalanceClosing($assetTypeId, $dsClose)
    {
        $result = $this->daoBalance->getOnDate($assetTypeId, $dsClose);
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

    private function getTransactions($assetTypeId, $dsClose)
    {
        /* first date should be after closing balance date */
        $tsFrom = $this->hlpPeriod->getTimestampNextFrom($dsClose);
        $tsTo = $this->hlpPeriod->getTimestampNextFrom(self::DATESTAMP_TO);
        $result = $this->daoTransaction->getForPeriod($assetTypeId, $tsFrom, $tsTo);
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