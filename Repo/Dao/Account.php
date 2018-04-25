<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Data\Account as Entity;
use Praxigento\Accounting\Repo\Data\Type\Asset as TypeAsset;

class Account
    extends \Praxigento\Core\App\Repo\Dao
{
    const ADMIN_WEBSITE_ID = Cfg::DEF_WEBSITE_ID_ADMIN;
    const BIND_CODE = 'code';
    const SYS_CUSTOMER_EMAIL = Cfg::SYS_CUSTOMER_EMAIL;
    /**
     * Cache for ID of the system customer.
     * @var int
     */
    protected $cachedSysCustId;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    )
    {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

    public function cacheReset()
    {
        $this->cachedSysCustId = null;
    }

    /**
     * @param \Praxigento\Accounting\Repo\Data\Account|array $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * Get all accounts by asset type code.
     *
     * @param string $assetTypeCode
     * @return \Praxigento\Accounting\Repo\Data\Account[]|null
     *
     * SELECT
     * paa.*
     * FROM prxgt_acc_type_asset pata
     * LEFT JOIN prxgt_acc_account paa
     * ON pata.id = paa.asset_type_id
     * WHERE pata.code = "PV";
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
        $on = $asAcc . '.' . Entity::A_ASSET_TYPE_ID . '=' . $asType . '.' . TypeAsset::A_ID;
        $cols = [
            Entity::A_ID,
            Entity::A_ASSET_TYPE_ID,
            Entity::A_BALANCE,
            Entity::A_CUST_ID
        ];
        $query->joinLeft([$asAcc => $tbl], $on, $cols);
        /* WHERE */
        $where = $asType . '.' . TypeAsset::A_CODE . '=:' . self::BIND_CODE;
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

    /**
     * Get all customer accounts.
     *
     * @param int $customerId
     * @return \Praxigento\Accounting\Repo\Data\Account[]|null
     */
    public function getAllByCustomerId($customerId)
    {
        $result = null;
        $where = '(' . Entity::A_CUST_ID . '=' . (int)$customerId . ')';
        $found = $this->get($where);
        if ($found) {
            /* TODO: use equal approach - '[]' instead of 'null' if not found */
            $result = $found;
        }
        return $result;
    }

    /**
     * Get asset type ID for the given account.
     *
     * @param int $accountId
     * @return int
     */
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

    /**
     * Get account data for the $customerId by $assetTypeId. Create new one if there is no account for this
     * customer/asset.
     *
     * @param int $customerId
     * @param int $assetTypeId
     * @return \Praxigento\Accounting\Repo\Data\Account|null
     */
    public function getByCustomerId($customerId, $assetTypeId)
    {
        $result = null;
        $where = '(' . Entity::A_CUST_ID . '=' . (int)$customerId . ')';
        $where = "$where AND (" . Entity::A_ASSET_TYPE_ID . '=' . (int)$assetTypeId . ')';
        $found = $this->get($where);
        if ($found && count($found)) {
            $result = reset($found);
        } else {
            /* there is no account for this customer/asset */
            $entity = new Entity();
            $entity->setCustomerId($customerId);
            $entity->setAssetTypeId($assetTypeId);
            $accId = $this->create($entity);
            $result = $this->getById($accId);
        }
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Accounting\Repo\Data\Account|bool
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    /**
     * Return system account ID for given asset type. Create new account if there is no yet system
     * account for this asset type.
     *
     * @param int $assetTypeId
     * @return int|null
     * @throws \Exception
     */
    public function getSystemAccountId($assetTypeId)
    {
        /* TODO: add cache for accounts ids */
        $result = null;
        $custId = $this->getSystemCustomerId();
        if ($custId) {
            $found = $this->getByCustomerId($custId, $assetTypeId);
            if ($found) {
                $result = $found->getId();
            } else {
                /* there is no yet system account for this asset type */
                $account = new Entity();
                $account->setAssetTypeId($assetTypeId);
                $account->setCustomerId($custId);
                $result = $this->create($account);
            }
        }
        return $result;
    }

    /**
     * Return MageID for customer that represents store owner in accounting. Create new system customer
     *if it is not exist yet.
     *
     * @return int
     */
    public function getSystemCustomerId()
    {
        if (is_null($this->cachedSysCustId)) {
            $conn = $this->conn;
            /* there is no cached value for the customer ID, select data from DB */
            $where = Cfg::E_CUSTOMER_A_EMAIL . '=' . $conn->quote(self::SYS_CUSTOMER_EMAIL);
            $data = $this->daoGeneric->getEntities(Cfg::ENTITY_MAGE_CUSTOMER, Cfg::E_CUSTOMER_A_ENTITY_ID,
                $where);
            if (count($data) == 0) {
                $bind = [
                    Cfg::E_CUSTOMER_A_WEBSITE_ID => self::ADMIN_WEBSITE_ID,
                    Cfg::E_CUSTOMER_A_EMAIL => self::SYS_CUSTOMER_EMAIL
                ];
                $id = $this->daoGeneric->addEntity(Cfg::ENTITY_MAGE_CUSTOMER, $bind);
                if ($id > 0) {
                    $this->cachedSysCustId = $id;
                }
            } else {
                $first = reset($data);
                $this->cachedSysCustId = $first[Cfg::E_CUSTOMER_A_ENTITY_ID];
            }
        }
        return $this->cachedSysCustId;
    }

    /**
     * Add/subtract value to/from account current balance.
     *
     * @param int $accountId
     * @param float $delta change value (negative or positive)
     * @return int number of updated rows in DB
     */
    public function updateBalance($accountId, $delta)
    {
        if ($delta < 0) {
            $exp = '(`' . Entity::A_BALANCE . '`-' . abs($delta) . ')';
        } else {
            $exp = '(`' . Entity::A_BALANCE . '`+' . abs($delta) . ')';
        }
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($exp);
        $bind = [Entity::A_BALANCE => $exp];
        $rowsUpdated = $this->updateById($accountId, $bind);
        return $rowsUpdated;
    }
}