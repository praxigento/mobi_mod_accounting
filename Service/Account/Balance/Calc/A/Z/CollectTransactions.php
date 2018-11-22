<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc\A\Z;

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
     * @param array $balances [accId => balance]
     * @param ETrans[] $trans
     * @return array [accId => EBalance[]]
     * @throws \Exception
     */
    public function exec($balances, $trans)
    {
        $result = [];
        foreach ($trans as $one) {
            $accDebit = $one->getDebitAccId();
            $accCredit = $one->getCreditAccId();
            $timestamp = $one->getDateApplied();
            $value = $one->getValue();
            /* date_applied is in UTC format */
            $date = $this->hlpPeriod->getPeriodCurrent($timestamp);
            /**
             * Process debit account:
             */
            /** @var EBalance $entry Get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($balances, $result, $accDebit, $date);
            /* change total debit for the entry */
            $totalDebit = $entry->getTotalDebit() + $value;
            $entry->setTotalDebit($totalDebit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() - $value;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accDebit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($balances[$accDebit])) {
                $balances[$accDebit] -= $value;
            } else {
                $balances[$accDebit] = -$value;
            }
            /**
             * Process credit account:
             */
            /* get balance on the transaction date (calculated or new) */
            $entry = $this->getBalanceEntry($balances, $result, $accCredit, $date);
            /* change total credit for the entry */
            $totalCredit = $entry->getTotalCredit() + $value;
            $entry->setTotalCredit($totalCredit);
            /* change close balance for the entry */
            $balanceClose = $entry->getBalanceClose() + $value;
            $entry->setBalanceClose($balanceClose);
            /* update calculated entries for result balances */
            $result[$accCredit][$date] = $entry;
            /* update current balances in the working var */
            if (isset($balances[$accCredit])) {
                $balances[$accCredit] += $value;
            } else {
                $balances[$accCredit] = $value;
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