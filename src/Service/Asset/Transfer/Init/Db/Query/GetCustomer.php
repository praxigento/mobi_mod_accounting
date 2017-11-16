<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Asset\Transfer\Init\Db\Query;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDwnlCust;


class GetCustomer
    extends \Praxigento\Core\Repo\Query\Builder
{
    /** Tables aliases for external usage */
    const AS_CUST = 'cust';
    const AS_DWNL = 'dwnl';

    /** Columns/expressions aliases for external usage */
    const A_EMAIL = 'email';
    const A_ID = 'id';
    const A_MLM_ID = 'mlmId';
    const A_NAME_FIRST = 'nameFirst';
    const A_NAME_LAST = 'nameLast';

    /** Bound variables names */
    const BND_CUST_ID = 'custId';

    /** Entities are used in the query */
    const E_CUST = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_DWNL = EDwnlCust::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_CUST;
        $asDwnl = self::AS_DWNL;

        /* FROM prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(EDwnlCust::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [
            self::A_MLM_ID => EDwnlCust::ATTR_HUMAN_REF
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN customer_entity */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCust;
        $cols = [
            self::A_ID => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_EMAIL => Cfg::E_CUSTOMER_A_EMAIL,
            self::A_NAME_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_NAME_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asDwnl . '.' . EDwnlCust::ATTR_CUSTOMER_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asCust . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=:' . self::BND_CUST_ID);

        return $result;
    }

}