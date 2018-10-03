<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing;


use Praxigento\Accounting\Repo\Data\Account as Account;

/**
 * Build query to get closing balance for all accounts of the given type on given date.
 * TODO: do we need to create service for the query?
 */
class ByAsset
    extends \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing
{

    /** Bound variables names */
    const BND_ASSET_TYPE_ID = 'assetTypeId';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = parent::build();    // this is independent query, ignore input query builder

        /* WHERE */
        $where = self::AS_ACC . '.' . Account::A_ASSET_TYPE_ID . '=:' . self::BND_ASSET_TYPE_ID;
        $result->where($where);

        /* result */
        return $result;
    }

}