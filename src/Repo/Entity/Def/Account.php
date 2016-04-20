<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Account as EntityAccount;
use Praxigento\Accounting\Repo\Entity\IAccount;
use Praxigento\Core\Repo\Def\Base;

class Account extends Base implements IAccount
{
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;

    /**
     * Account constructor.
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoBasic
    ) {
        parent::__construct($resource);
        $this->_repoBasic = $repoBasic;
    }

    public function create($data)
    {
        $result = null;
        $entity = EntityAccount::ENTITY_NAME;
        $id = $this->_repoBasic->addEntity($entity, $data);
        if ($id) {
            $result = $data;
            $result[EntityAccount::ATTR_ID] = $id;
        }
        return $result;
    }

    public function getByCustomerId($customerId, $assetTypeId)
    {
        $result = null;
        $entity = EntityAccount::ENTITY_NAME;
        $whereCust = EntityAccount::ATTR_CUST_ID . '=' . (int)$customerId;
        $whereAsset = EntityAccount::ATTR_ASSET_TYPE_ID . '=' . (int)$assetTypeId;
        $where = "$whereCust AND $whereAsset";
        $found = $this->_repoBasic->getEntities($entity, null, $where);
        if ($found && is_array($found)) {
            $result = reset($found);
        }
        return $result;
    }

    public function getById($accountId)
    {
        $entity = EntityAccount::ENTITY_NAME;
        $pk = [EntityAccount::ATTR_ID => $accountId];
        $result = $this->_repoBasic->getEntityByPk($entity, $pk);
        return $result;
    }

    public function updateBalance($accountId, $delta)
    {
        $tbl = $this->_conn->getTableName(EntityAccount::ENTITY_NAME);
        /* wrap expression into \Zend_Db_Expr */
        if ($delta < 0) {
            $exp = new \Zend_Db_Expr(EntityAccount::ATTR_BALANCE . '-' . abs($delta));
        } else {
            $exp = new \Zend_Db_Expr(EntityAccount::ATTR_BALANCE . '+' . abs($delta));
        }
        $bind = [EntityAccount::ATTR_BALANCE => $exp];
        $where = EntityAccount::ATTR_ID . '=' . $accountId;
        $rowsUpdated = $this->_conn->update($tbl, $bind, $where);
        return $rowsUpdated;
    }
}