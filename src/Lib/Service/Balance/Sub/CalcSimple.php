<?php
/**
 * Simple in-memoty balance calculation.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Service\Balance\Sub;


use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Core\Tool\IPeriod;

class CalcSimple
{
    /**
     * @var \Praxigento\Core\Tool\IPeriod
     */
    private $_toolPeriod;

    /**
     * CalcSimple constructor.
     */
    public function __construct(IPeriod $toolPeriod)
    {
        $this->_toolPeriod = $toolPeriod;
    }

    public function calcBalances($currentBalances, $transactions)
    {
        $result = [];
        foreach ($transactions as $one) {
            $accDebit = $one[Transaction::ATTR_DEBIT_ACC_ID];
            $accCredit = $one[Transaction::ATTR_CREDIT_ACC_ID];
            $timestamp = $one[Transaction::ATTR_DATE_APPLIED];
            $date = $this->_toolPeriod->getPeriodCurrent($timestamp, IPeriod::TYPE_DAY);
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
                if (isset($result[$accDebit])) {
                    $last = end($result[$accDebit]);
                    $data[Balance::ATTR_BALANCE_OPEN] = $last[Balance::ATTR_BALANCE_CLOSE];
                } elseif (isset($currentBalances[$accDebit])) {
                    $data[Balance::ATTR_BALANCE_OPEN] = $currentBalances[$accDebit][Balance::ATTR_BALANCE_CLOSE];
                }
            }
            /* change debit related values */
            $data[Balance::ATTR_TOTAL_DEBIT] += $changeValue;
            $data[Balance::ATTR_BALANCE_CLOSE] -= $changeValue;
            $result[$accDebit][$date] = $data;
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
                if (isset($result[$accCredit])) {
                    $last = end($result[$accCredit]);
                    $data[Balance::ATTR_BALANCE_OPEN] = $last[Balance::ATTR_BALANCE_CLOSE];
                } elseif (isset($currentBalances[$accCredit])) {
                    $data[Balance::ATTR_BALANCE_OPEN] = $currentBalances[$accCredit][Balance::ATTR_BALANCE_CLOSE];
                }
            }
            /* change credit related values */
            $data[Balance::ATTR_TOTAL_CREDIT] += $changeValue;
            $data[Balance::ATTR_BALANCE_CLOSE] += $changeValue;
            $result[$accCredit][$date] = $data;
        }
        return $result;
    }
}