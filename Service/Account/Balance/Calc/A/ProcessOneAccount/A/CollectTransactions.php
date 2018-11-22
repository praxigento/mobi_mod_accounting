<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneAccount\A;

use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;

/**
 * Collect transactions and compose daily balances updates.
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

    /**
     * Collect transactions and compose daily balances updates.
     *
     * @param int $accId
     * @param float $balance
     * @param ETrans[] $trans
     * @return EBalance[]
     * @throws \Exception
     */
    public function exec($accId, $balance, $trans)
    {
        $result = [];
        $currentBalance = $balance;
        foreach ($trans as $one) {
            $accDebit = $one->getDebitAccId();
            $accCredit = $one->getCreditAccId();
            $timestamp = $one->getDateApplied();
            /* date_applied is in UTC format */
            $date = $this->hlpPeriod->getPeriodCurrent($timestamp);
            $changeValue = $one->getValue();
            /**
             * Process debit account:
             */
            /** @var EBalance $entry Get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($currentBalance, $result, $accDebit, $date);
            /* change total debit for the entry */
            $totalDebit = $entry->getTotalDebit() + $changeValue;
            $entry->setTotalDebit($totalDebit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() - $changeValue;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accDebit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($currentBalance[$accDebit])) {
                $currentBalance[$accDebit] -= $changeValue;
            } else {
                $currentBalance[$accDebit] = -$changeValue;
            }
            /**
             * Process credit account:
             */
            /* get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($currentBalance, $result, $accCredit, $date);
            /* change total credit for the entry */
            $totalCredit = $entry->getTotalCredit() + $changeValue;
            $entry->setTotalCredit($totalCredit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() + $changeValue;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accCredit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($currentBalance[$accCredit])) {
                $currentBalance[$accCredit] += $changeValue;
            } else {
                $currentBalance[$accCredit] = $changeValue;
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
}