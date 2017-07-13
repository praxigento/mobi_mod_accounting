<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Entity\Account as Entity;
use Praxigento\Accounting\Data\Entity\Type\Asset as TypeAsset;

class Account
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Accounting\Repo\Entity\IAccount
{
    const ADMIN_WEBSITE_ID = Cfg::DEF_WEBSITE_ID_ADMIN;
    const BIND_CODE = 'code';
    const CUSTOMER_REPRESENTATIVE_EMAIL = Cfg::CUSTOMER_REPRESENTATIVE_EMAIL;
    /**
     * Cache for ID of the representative customer.
     * @var int
     */
    protected $cachedRepresCustId;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    public function cacheReset()
    {
        $this->cachedRepresCustId = null;
    }

    /**
     * SELECT
     * paa.*
     * FROM prxgt_acc_type_asset pata
     * LEFT JOIN prxgt_acc_account paa
     * ON pata.id = paa.asset_type_id
     * WHERE pata.code = "PV";
     *
     * @inheritdoc
     */
    public function getAllByAssetTypeCode($assetTypeCode)
    {
        /* aliases and tables */
        $asType = 'at';
        $asAcc = 'acc';
        /* SELECT FROM prxgt_acc_type_asset */
        $query = $this->conn->select();
        $tbl = $this->resource->getTableName(TypeAsset::ENTITY_NAME);
        $cols = [];
        $query->from([$asType => $tbl], $cols);
        /* LEFT JOIN prxgt_acc_account */
        $tbl = $this->resource->getTableName(Entity::ENTITY_NAME);
        $on = $asAcc . '.' . Entity::ATTR_ASSET_TYPE_ID . '=' . $asType . '.' . TypeAsset::ATTR_ID;
        $cols = [
            Entity::ATTR_ID,
            Entity::ATTR_ASSET_TYPE_ID,
            Entity::ATTR_BALANCE,
            Entity::ATTR_CUST_ID
        ];
        $query->joinLeft([$asAcc => $tbl], $on, $cols);
        /* WHERE */
        $where = $asType . '.' . TypeAsset::ATTR_CODE . '=:' . self::BIND_CODE;
        $query->where($where);
        /* bind vars and fetch results */
        $bind = [self::BIND_CODE => $assetTypeCode];
        $rs = $this->conn->fetchAll($query, $bind);
        $result = [];
        foreach ($rs as $one) {
            $item = new Entity($one);
            $result[$item->getId()] = $item;
        }
        return $result;
    }

    public function getAllByCustomerId($customerId)
    {
        $result = null;
        $where = '(' . Entity::ATTR_CUST_ID . '=' . (int)$customerId . ')';
        $found = $this->get($where);
        if ($found) {
            /* return all entries */
            $entries = [];
            foreach ($found as $item) {
                $entry = $this->_createEntityInstance($item->get());
                $entries[] = $entry;
            }
            $result = $entries;
        }
        return $result;
    }

    public function getAssetTypeId($accountId)
    {
        $result = null;
        /** @var Entity $entity */
        $entity = $this->getById($accountId);
        if ($entity) {
            $result = $entity->getAssetTypeId();
        }
        return $result;
    }

    public function getByCustomerId($customerId, $assetTypeId)
    {
        $result = null;
        $where = '(' . Entity::ATTR_CUST_ID . '=' . (int)$customerId . ')';
        $where = "$where AND (" . Entity::ATTR_ASSET_TYPE_ID . '=' . (int)$assetTypeId . ')';
        $found = $this->get($where);
        if ($found && count($found)) {
            $data = reset($found);
            $result = $this->_createEntityInstance($data->get());
        }
        return $result;
    }

    public function getRepresentativeAccountId($assetTypeId)
    {
        /* TODO: add cache for accounts ids */
        $result = null;
        $custId = $this->getRepresentativeCustomerId();
        if ($custId) {
            $found = $this->getByCustomerId($custId, $assetTypeId);
            if ($found) {
                $result = $found->getId();
            }
        }
        return $result;
    }

    public function getRepresentativeCustomerId()
    {
        if (is_null($this->cachedRepresCustId)) {
            $conn = $this->conn;
            /* there is no cached value for the customer ID, select data from DB */
            $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $conn->quote(self::CUSTOMER_REPRESENTATIVE_EMAIL);
            $data = $this->_repoGeneric->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID,
                $where);
            if (count($data) == 0) {
                $bind = [
                    Cfg::E_CUSTOMER_A_WEBSITE_ID => self::ADMIN_WEBSITE_ID,
                    Cfg::E_CUSTOMER_A_EMAIL => self::CUSTOMER_REPRESENTATIVE_EMAIL
                ];
                $id = $this->_repoGeneric->addEntity(Cfg::ENTITY_MAGE_CUSTOMER, $bind);
                if ($id > 0) {
                    $this->cachedRepresCustId = $id;
                }
            } else {
                $first = reset($data);
                $this->cachedRepresCustId = $first[Cfg::E_CUSTOMER_A_ENTITY_ID];
            }
        }
        return $this->cachedRepresCustId;
    }

    public function updateBalance($accountId, $delta)
    {
        if ($delta < 0) {
            $exp = '(`' . Entity::ATTR_BALANCE . '`-' . abs($delta) . ')';
        } else {
            $exp = '(`' . Entity::ATTR_BALANCE . '`+' . abs($delta) . ')';
        }
        $exp = new \Praxigento\Core\Repo\Query\Expression($exp);
        $bind = [Entity::ATTR_BALANCE => $exp];
        $rowsUpdated = $this->updateById($accountId, $bind);
        return $rowsUpdated;
    }
}