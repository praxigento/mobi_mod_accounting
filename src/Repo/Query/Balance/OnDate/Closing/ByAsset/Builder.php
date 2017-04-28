<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset;


use Praxigento\Accounting\Data\Entity\Account as Account;

/**
 * Build query to get closing balance for all accounts of the given type on given date.
 */
class Builder
    extends \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\Builder
{

    /** Bound variables names */
    const BIND_ASSET_TYPE_ID = 'assetTypeId';


    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $result = parent::getSelectQuery();    // this is independent query, ignore input query builder

        /* WHERE */
        $where = self::AS_ACC . Account::ATTR_ASSET_TYPE_ID . '=:' . self::BIND_ASSET_TYPE_ID;
        $result->where($where);

        /* result */
        return $result;
    }
}