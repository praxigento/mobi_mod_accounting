<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\A;

use Praxigento\Accounting\Repo\Data\Balance as EBalance;

/**
 * Build query to get accounts & max date less or equal to given from balances.
 *
 * This is auxiliary query to be used in other balance queries. Therefore we use complex names for query's components.
 */
class MaxDates
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
    const BND_MAX_DATE = 'balanceDateMax';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();    // this is independent query, ignore input query builder

        /* create shortcuts for table aliases */
        $asBal = self::AS_BAL;

        /* SELECT FROM prxgt_acc_balance */
        $tbl = $this->resource->getTableName(EBalance::ENTITY_NAME);
        $as = $asBal;
        $expValue = "MAX($asBal." . EBalance::A_DATE . ")";
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($expValue);
        $cols = [
            self::A_ACC_ID => EBalance::A_ACCOUNT_ID,
            self::A_DATE_MAX => $exp
        ];
        $result->from([$as => $tbl], $cols);

        /* WHERE */
        $result->where($asBal . '.' . EBalance::A_DATE . '<=:' . self::BND_MAX_DATE);

        /* GROUP */
        $result->group($asBal . '.' . EBalance::A_ACCOUNT_ID);

        return $result;
    }
}