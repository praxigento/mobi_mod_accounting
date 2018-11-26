<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\Z;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;

/**
 * Update daily balances.
 */
class UpdateBalances
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance
    ) {
        $this->logger = $logger;
        $this->daoAccount = $daoAccount;
        $this->daoBalance = $daoBalance;
    }


    public function exec($updates)
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