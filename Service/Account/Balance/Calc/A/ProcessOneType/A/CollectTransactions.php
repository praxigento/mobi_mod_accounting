<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType\A;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;

/**
 * Collect daily balances entries using transactions.
 */
class CollectTransactions
{
    /**
     * @var \Praxigento\Core\Api\Helper\Period
     */
    private $hlpPeriod;

    public function __construct(
        \Praxigento\Core\Api\Helper\Period $hlpPeriod
    ) {
        $this->hlpPeriod = $hlpPeriod;
    }

    public function exec($balances, $trans)
    {
        $result = [];
        /** @var array $currentBalances [accId => balance] */
        $currentBalances = $this->prepareBalances($balances);
        $prepTrans = $this->prepareTransactions($trans);
        foreach ($prepTrans as $one) {
            $accDebit = $one[ETrans::A_DEBIT_ACC_ID];
            $accCredit = $one[ETrans::A_CREDIT_ACC_ID];
            $timestamp = $one[ETrans::A_DATE_APPLIED];
            /* date_applied is in UTC format */
            $date = $this->hlpPeriod->getPeriodCurrent($timestamp);
            $changeValue = $one[ETrans::A_VALUE];
            /**
             * Process debit account:
             */
            /** @var EBalance $entry Get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($currentBalances, $result, $accDebit, $date);
            /* change total debit for the entry */
            $totalDebit = $entry->getTotalDebit() + $changeValue;
            $entry->setTotalDebit($totalDebit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() - $changeValue;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accDebit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($currentBalances[$accDebit])) {
                $currentBalances[$accDebit] -= $changeValue;
            } else {
                $currentBalances[$accDebit] = -$changeValue;
            }
            /**
             * Process credit account:
             */
            /* get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($currentBalances, $result, $accCredit, $date);
            /* change total credit for the entry */
            $totalCredit = $entry->getTotalCredit() + $changeValue;
            $entry->setTotalCredit($totalCredit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() + $changeValue;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accCredit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($currentBalances[$accCredit])) {
                $currentBalances[$accCredit] += $changeValue;
            } else {
                $currentBalances[$accCredit] = $changeValue;
            }
        }
        return $result;
    }

    /**
     * Extract existing balance on the date or create new empty entity.
     *
     * @param array $current [accId => balance]
     * @param EBalance[] $balances
     * @param $accId
     * @param $datestamp
     * @return EBalance
     * @throws \Exception
     */
    private function getBalanceEntry($current, $balances, $accId, $datestamp)
    {
        if (isset($balances[$accId][$datestamp])) {
            /* there is data for this account on this date */
            $result = $balances[$accId][$datestamp];
        } else {
            /* there is NO data for this account on this date */
            $result = new EBalance();
            $result->setAccountId($accId);
            $result->setDate($datestamp);
            $result->setBalanceOpen(0);
            $result->setTotalDebit(0);
            $result->setTotalCredit(0);
            $result->setBalanceClose(0);
            if (isset($current[$accId])) {
                /* we need to update entry balances for newly created entry */
                $result->setBalanceOpen($current[$accId]);
                $result->setBalanceClose($current[$accId]);
            }
        }
        return $result;
    }

    /**
     * @param array $balances @see \Praxigento\Accounting\Repo\Dao\Balance::getOnDate
     * @return array [accId => balanceClose]
     */
    private function prepareBalances($balances)
    {
        $result = [];
        foreach ($balances as $balance) {
            $accountId = $balance[EAccount::A_ID];
            $value = $balance[EBalance::A_BALANCE_CLOSE];
            $result[$accountId] = $value;
        }
        return $result;
    }

    /**
     * Re-order transactions by date_applied
     *
     * @param array $transactions @see \Praxigento\Accounting\Repo\Dao\Balance::getOnDate
     * @return array
     */
    private function prepareTransactions($transactions)
    {
        usort($transactions, function ($a, $b) {
            $aTs = $a[ETrans::A_DATE_APPLIED];
            $bTs = $b[ETrans::A_DATE_APPLIED];
            $result = 0;
            if ($aTs < $bTs) {
                $result = -1;
            } elseif ($aTs > $bTs) {
                $result = 1;
            }
            return $result;
        });
        return $transactions;
    }
}