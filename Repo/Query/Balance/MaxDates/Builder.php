<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\MaxDates;

use Praxigento\Accounting\Repo\Data\Balance as Balance;

/**
 * Build query to get accounts & max date less or equal to given from balances.
 *
 * This is auxiliary query to be used in other balance queries. Therefore we use complex names for query's components.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /**
     * Tables aliases.
     */
    const AS_BAL = 'balanceDateMax';

    /**
     * Attributes aliases.
     */
    const A_ACC_ID = 'balMaxDateAccId';
    const A_DATE_MAX = 'balMaxDate';

    /** Bound variables names */
    const BIND_MAX_DATE = 'balanceDateMax';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();    // this is independent query, ignore input query builder
        /* create shortcuts for table aliases */
        $asBal = self::AS_BAL;

        /* SELECT FROM prxgt_acc_balance */
        $tbl = $this->resource->getTableName(Balance::ENTITY_NAME);
        $as = $asBal;
        $expValue = "MAX($asBal." . Balance::A_DATE . ")";
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($expValue);
        $cols = [
            self::A_ACC_ID => Balance::A_ACCOUNT_ID,
            self::A_DATE_MAX => $exp
        ];
        $result->from([$as => $tbl], $cols);

        /* WHERE */
        $result->where($asBal . '.' . Balance::A_DATE . '<=:' . self::BIND_MAX_DATE);

        /* GROUP */
        $result->group($asBal . '.' . Balance::A_ACCOUNT_ID);
        /* result */
        return $result;
    }
}