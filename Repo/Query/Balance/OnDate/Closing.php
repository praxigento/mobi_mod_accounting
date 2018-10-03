<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\OnDate;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\A\MaxDates as QMaxDates;
use Praxigento\Core\App\Repo\Query\Expression as AnExpression;

/**
 * Query to get closing balance for all accounts on given date.
 */
class Closing
    extends \Praxigento\Core\App\Repo\Query\Builder
    implements \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing
{
    /** @var \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\A\MaxDates */
    private $qMaxDates;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\A\MaxDates $qMaxDates
    ) {
        parent::__construct($resource);
        $this->qMaxDates = $qMaxDates;
    }

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();    // this is independent query, ignore input query builder

        /* create shortcuts for table aliases */
        $asAcc = self::AS_ACC;
        $asBal = self::AS_BALANCE;
        $asMax = self::AS_DATE_MAX;

        /* SELECT FROM prxgt_acc_account */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAcc;
        $cols = [
            self::A_ACC_ID => EAccount::A_ID,
            self::A_CUST_ID => EAccount::A_CUST_ID
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN MAX_DATES */
        $queryMaDates = $this->qMaxDates->build();
        $tbl = $queryMaDates;
        $as = $asMax;
        $cols = [];
        $on = $asMax . '.' . QMaxDates::A_ACC_ID . '='
            . $asAcc . '.' . EAccount::A_ID;
        $result->joinLeft([$as => $tbl], $on, $cols);

        /* LEFT JOIN prxgt_acc_balance (to get closing balances on found max dates) */
        $tbl = $this->resource->getTableName(EBalance::ENTITY_NAME);
        $as = $asBal;
        $cols = [
            self::A_BALANCE => EBalance::A_BALANCE_CLOSE
        ];
        $on = $asBal . '.' . EBalance::A_ACCOUNT_ID . "=$asMax." . QMaxDates::A_ACC_ID;
        $on .= " AND $asBal." . EBalance::A_DATE . "=$asMax." . QMaxDates::A_DATE_MAX;
        $result->joinLeft([$as => $tbl], $on, $cols);

        /* WHERE */
        $expValue = "$asBal." . EBalance::A_DATE . " IS NOT NULL";
        $exp = new AnExpression($expValue);
        $result->where($exp);

        /* result */
        return $result;
    }

}