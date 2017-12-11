<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Query\Trans\Get\FirstDate\ByAssetType;

use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETrans;
use Praxigento\Accounting\Repo\Entity\Data\Type\Asset as ETypeAsset;

/**
 * Build query to get transactions for the customer.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Def\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ACC = 'paa';
    const AS_TRANS = 'pat';
    const AS_TYPE = 'pata';

    /** Columns/expressions aliases for external usage ('underscore' naming for database fields; 'camelCase' naming for aliases) */
    const A_DATE_APPLIED = 'dateApplied';

    /** Bound variables names ('camelCase' naming) */
    const BND_ASSET_TYPE_CODE = 'assetTypeCode';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select(); // to build primary queries (started from SELECT)

        /* define tables aliases for internal usage (in this method) */
        $asAcc = self::AS_ACC;
        $asTrans = self::AS_TRANS;
        $asType = self::AS_TYPE;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(ETrans::ENTITY_NAME);    // name with prefix
        $as = $asTrans;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_DATE_APPLIED => ETrans::ATTR_DATE_APPLIED
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $as = $asAcc;
        $cols = [];
        $cond = $as . '.' . EAccount::ATTR_ID . '=' . $asTrans . '.' . ETrans::ATTR_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_type_asset pata */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asType;
        $cols = [];
        $cond = $as . '.' . ETypeAsset::ATTR_ID . '=' . $asAcc . '.' . EAccount::ATTR_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);


        /* WHERE */
        $where = $asType . '.' . ETypeAsset::ATTR_CODE . '=:' . self::BND_ASSET_TYPE_CODE;
        $result->where($where);

        /* ORDER & LIMIT */
        $result->order($asTrans . '.' . ETrans::ATTR_DATE_APPLIED . ' ASC');
        $result->limit(1);

        return $result;
    }


}