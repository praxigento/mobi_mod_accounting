<?php
/**
 * Simple in-memory balance calculation.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc;


use Praxigento\Accounting\Repo\Entity\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETransaction;

class Simple
{
    /**
     * @var \Praxigento\Core\Tool\IPeriod
     */
    private $hlpPeriod;

    public function __construct(
        \Praxigento\Core\Tool\IPeriod $hlpPeriod
    ) {
        $this->hlpPeriod = $hlpPeriod;
    }

    /**
     * Walk trough transactions ordered by date applied and compose balances.
     *
     * @param array $currentBalances balances for the begin of the period.
     * @param array $transactions all transactions for the period
     * @return array balances for the period
     */
    public function exec($currentBalances, $transactions)
    {
        $result = [];

        /* convert current balances for internal format and sort transactions by date applied (already sorted in DB, but...) */
        $balPrepared = $this->prepareBalances($currentBalances);
        $transPrepared = $this->prepareTransactions($transactions);

        foreach ($transPrepared as $one) {
            $accDebit = $one[ETransaction::ATTR_DEBIT_ACC_ID];
            $accCredit = $one[ETransaction::ATTR_CREDIT_ACC_ID];
            $timestamp = $one[ETransaction::ATTR_DATE_APPLIED];
            $date = $this->hlpPeriod->getPeriodCurrent($timestamp, +1);
            $changeValue = $one[ETransaction::ATTR_VALUE];
            /**
             * process debit account
             */
            /* get calculated balance on the date*/
            if (isset($result[$accDebit][$date])) {
                /* there is data for this account on this date */
                $data = $result[$accDebit][$date];
            } else {
                /* there is NO data for this account on this date */
                $data = [
                    EBalance::ATTR_ACCOUNT_ID => $accDebit,
                    EBalance::ATTR_DATE => $date,
                    EBalance::ATTR_BALANCE_OPEN => 0,
                    EBalance::ATTR_TOTAL_DEBIT => 0,
                    EBalance::ATTR_TOTAL_CREDIT => 0,
                    EBalance::ATTR_BALANCE_CLOSE => 0,
                ];
                /* we need to update opening balance */
                if (isset($balPrepared[$accDebit])) {
                    $data[EBalance::ATTR_BALANCE_OPEN] = $balPrepared[$accDebit];
                    $data[EBalance::ATTR_BALANCE_CLOSE] = $balPrepared[$accDebit];
                }
            }
            /* change debit related values */
            $data[EBalance::ATTR_TOTAL_DEBIT] += $changeValue;
            $data[EBalance::ATTR_BALANCE_CLOSE] -= $changeValue;
            $result[$accDebit][$date] = $data;
            if (isset($balPrepared[$accDebit])) {
                $balPrepared[$accDebit] -= $changeValue;
            } else {
                $balPrepared[$accDebit] = -$changeValue;
            }
            /**
             * process credit account
             */
            /* get calculated balance on the date*/
            if (isset($result[$accCredit][$date])) {
                /* there is data for this account on this date */
                $data = $result[$accCredit][$date];
            } else {
                /* there is NO data for this account on this date */
                $data = [
                    EBalance::ATTR_ACCOUNT_ID => $accCredit,
                    EBalance::ATTR_DATE => $date,
                    EBalance::ATTR_BALANCE_OPEN => 0,
                    EBalance::ATTR_TOTAL_DEBIT => 0,
                    EBalance::ATTR_TOTAL_CREDIT => 0,
                    EBalance::ATTR_BALANCE_CLOSE => 0,
                ];
                /* we need to update opening balance */
                if (isset($balPrepared[$accCredit])) {
                    $data[EBalance::ATTR_BALANCE_OPEN] = $balPrepared[$accCredit];
                    $data[EBalance::ATTR_BALANCE_CLOSE] = $balPrepared[$accCredit];
                }
            }
            /* change credit related values */
            $data[EBalance::ATTR_TOTAL_CREDIT] += $changeValue;
            $data[EBalance::ATTR_BALANCE_CLOSE] += $changeValue;
            $result[$accCredit][$date] = $data;
            if (isset($balPrepared[$accCredit])) {
                $balPrepared[$accCredit] += $changeValue;
            } else {
                $balPrepared[$accCredit] = $changeValue;
            }
        }
        return $result;
    }

    private function prepareBalances($balances)
    {
        $result = [];
        foreach ($balances as $balance) {
            $accountId = $balance[\Praxigento\Accounting\Repo\Entity\Data\Account::ATTR_ID];
            $value = $balance[EBalance::ATTR_BALANCE_CLOSE];
            $result[$accountId] = $value;
        }
        return $result;
    }

    /**
     * Order transactions by date_applied
     *
     * @param array $transactions
     * @return array
     */
    private function prepareTransactions($transactions)
    {
        usort($transactions, function ($a, $b) {
            $aTs = $a[ETransaction::ATTR_DATE_APPLIED];
            $bTs = $b[ETransaction::ATTR_DATE_APPLIED];
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