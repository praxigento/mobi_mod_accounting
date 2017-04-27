<?php
/**
 * Simple in-memoty balance calculation.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance\Sub;


use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Core\Tool\Def\Period;
use Praxigento\Core\Tool\IPeriod;

class CalcSimple
{
    /**
     * @var \Praxigento\Core\Tool\IPeriod
     */
    private $toolPeriod;

    /**
     * CalcSimple constructor.
     */
    public function __construct(IPeriod $toolPeriod)
    {
        $this->toolPeriod = $toolPeriod;
    }

    /**
     * Walk trough transactions ordered by date applied and compose balances.
     *
     * @param array $currentBalances balances for the begin of the period.
     * @param array $transactions all transactions for the period
     * @return array balances for the period
     */
    public function calcBalances($currentBalances, $transactions)
    {
        $result = [];

        /* convert current balances for internal format and sort transactions by date applied (already sorted in DB, but...) */
        $balPrepared = $this->prepareBalances($currentBalances);
        $transPrepared = $this->prepareTransactions($transactions);

        foreach ($transPrepared as $one) {
            $accDebit = $one[Transaction::ATTR_DEBIT_ACC_ID];
            $accCredit = $one[Transaction::ATTR_CREDIT_ACC_ID];
            $timestamp = $one[Transaction::ATTR_DATE_APPLIED];
            $date = $this->toolPeriod->getPeriodCurrent($timestamp, Period::TYPE_DAY, false);
            $changeValue = $one[Transaction::ATTR_VALUE];
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
                    Balance::ATTR_ACCOUNT_ID => $accDebit,
                    Balance::ATTR_DATE => $date,
                    Balance::ATTR_BALANCE_OPEN => 0,
                    Balance::ATTR_TOTAL_DEBIT => 0,
                    Balance::ATTR_TOTAL_CREDIT => 0,
                    Balance::ATTR_BALANCE_CLOSE => 0,
                ];
                /* we need to update opening balance */
                if (isset($balPrepared[$accDebit])) {
                    $data[Balance::ATTR_BALANCE_OPEN] = $balPrepared[$accDebit];
                    $data[Balance::ATTR_BALANCE_CLOSE] = $balPrepared[$accDebit];
                }
            }
            /* change debit related values */
            $data[Balance::ATTR_TOTAL_DEBIT] += $changeValue;
            $data[Balance::ATTR_BALANCE_CLOSE] -= $changeValue;
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
                    Balance::ATTR_ACCOUNT_ID => $accCredit,
                    Balance::ATTR_DATE => $date,
                    Balance::ATTR_BALANCE_OPEN => 0,
                    Balance::ATTR_TOTAL_DEBIT => 0,
                    Balance::ATTR_TOTAL_CREDIT => 0,
                    Balance::ATTR_BALANCE_CLOSE => 0,
                ];
                /* we need to update opening balance */
                if (isset($balPrepared[$accCredit])) {
                    $data[Balance::ATTR_BALANCE_OPEN] = $balPrepared[$accCredit];
                    $data[Balance::ATTR_BALANCE_CLOSE] = $balPrepared[$accCredit];
                }
            }
            /* change credit related values */
            $data[Balance::ATTR_TOTAL_CREDIT] += $changeValue;
            $data[Balance::ATTR_BALANCE_CLOSE] += $changeValue;
            $result[$accCredit][$date] = $data;
            if (isset($balPrepared[$accCredit])) {
                $balPrepared[$accCredit] += $changeValue;
            } else {
                $balPrepared[$accCredit] = $changeValue;
            }
        }
        return $result;
    }

    protected function prepareBalances($balances)
    {
        $result = [];
        foreach ($balances as $balance) {
            $accountId = $balance[\Praxigento\Accounting\Data\Entity\Account::ATTR_ID];
            $value = $balance[Balance::ATTR_BALANCE_CLOSE];
            $result[$accountId] = $value;
        }
        return $result;
    }

    /**
     * Order transactions by date_applied
     *
     * @param array $transactions
     */
    protected function prepareTransactions($transactions)
    {
        usort($transactions, function ($a, $b) {
            $aTs = $a[Transaction::ATTR_DATE_APPLIED];
            $bTs = $b[Transaction::ATTR_DATE_APPLIED];
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