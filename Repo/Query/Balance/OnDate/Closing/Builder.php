<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing;

use Praxigento\Accounting\Repo\Data\Account as Account;
use Praxigento\Accounting\Repo\Data\Balance as Balance;
use Praxigento\Accounting\Repo\Query\Balance\MaxDates\Builder as QMaxDates;

/**
 * Build query to get closing balance for all accounts on given date.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /**
     * Tables aliases.
     */
    const AS_ACC = 'acc';
    const AS_BALANCE = 'bal';
    const AS_DATE_MAX = 'dateMax';

    /**
     * Attributes aliases.
     */
    const A_ACC_ID = 'accId';
    const A_BALANCE = 'balance';
    const A_CUST_ID = 'custId';
    const A_DATE_MAX = 'dateMax';

    /** Bound variables names */
    const BIND_MAX_DATE = QMaxDates::BIND_MAX_DATE;

    /** @var \Praxigento\Accounting\Repo\Query\Balance\MaxDates\Builder */
    protected $qbldMaxDates;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Repo\Query\Balance\MaxDates\Builder $qbldOnDate
    ) {
        parent::__construct($resource);
        $this->qbldMaxDates = $qbldOnDate;
    }

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();    // this is independent query, ignore input query builder
        /* create shortcuts for table aliases */
        $asAcc = self::AS_ACC;
        $asBal = self::AS_BALANCE;
        $asMax = self::AS_DATE_MAX;

        /* SELECT FROM prxgt_acc_account */
        $tbl = $this->resource->getTableName(Account::ENTITY_NAME);
        $as = $asAcc;
        $cols = [
            self::A_ACC_ID => Account::A_ID,
            self::A_CUST_ID => Account::A_CUST_ID
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN MAX_DATES */
        $queryMaDates = $this->qbldMaxDates->build();
        $tbl = $queryMaDates;
        $as = $asMax;
        $cols = [];
        $on = $asMax . '.' . QMaxDates::A_ACC_ID . '='
            . $asAcc . '.' . Account::A_ID;
        $result->joinLeft([$as => $tbl], $on, $cols);

        /* LEFT JOIN prxgt_acc_balance (to get closing balances on found max dates) */
        $tbl = $this->resource->getTableName(Balance::ENTITY_NAME);
        $as = $asBal;
        $cols = [
            self::A_BALANCE => Balance::A_BALANCE_CLOSE
        ];
        $on = $asBal . '.' . Balance::A_ACCOUNT_ID . "=$asMax." . QMaxDates::A_ACC_ID;
        $on .= " AND $asBal." . Balance::A_DATE . "=$asMax." . QMaxDates::A_DATE_MAX;
        $result->joinLeft([$as => $tbl], $on, $cols);

        /* WHERE */
        $expValue = "$asBal." . Balance::A_DATE . " IS NOT NULL";
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($expValue);
        $result->where($exp);

        /* result */
        return $result;
    }

}