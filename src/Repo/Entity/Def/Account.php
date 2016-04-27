<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Account as Entity;
use Praxigento\Accounting\Repo\Entity\IAccount;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;

class Account extends BaseEntityRepo implements IAccount
{

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
    public function getByCustomerId($customerId, $assetTypeId)
    {
        $whereCust = Entity::ATTR_CUST_ID . '=' . (int)$customerId;
        $whereAsset = Entity::ATTR_ASSET_TYPE_ID . '=' . (int)$assetTypeId;
        $where = "$whereCust AND $whereAsset";
        $result = $this->get($where);
        if ($result) {
            $data = reset($result);
            $result = $this->_createEntityInstance($data);
        }
        return $result;
    }

    /** @inheritdoc */
    public function updateBalance($accountId, $delta)
    {
        $tbl = $this->_conn->getTableName(Entity::ENTITY_NAME);
        /* wrap expression into \Zend_Db_Expr */
        if ($delta < 0) {
            $exp = new \Zend_Db_Expr(Entity::ATTR_BALANCE . '-' . abs($delta));
        } else {
            $exp = new \Zend_Db_Expr(Entity::ATTR_BALANCE . '+' . abs($delta));
        }
        $bind = [Entity::ATTR_BALANCE => $exp];
        $where = Entity::ATTR_ID . '=' . $accountId;
        $rowsUpdated = $this->_conn->update($tbl, $bind, $where);
        return $rowsUpdated;
    }
}