<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Account as Entity;

class Account
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Accounting\Repo\Entity\IAccount
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
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

    public function getByCustomerId($customerId, $assetTypeId = null)
    {
        $where = '(' . Entity::ATTR_CUST_ID . '=' . (int)$customerId . ')';
        if ($assetTypeId) {
            $where = "$where AND (" . Entity::ATTR_ASSET_TYPE_ID . '=' . (int)$assetTypeId . ')';
        }
        $result = $this->get($where);
        if ($result) {
            if (is_null($assetTypeId)) {
                /* return all entries */
                $dataObjects = [];
                foreach ($result as $item) {
                    $obj = $this->_createEntityInstance($item);
                    $dataObjects[] = $obj;
                }
                $result = $dataObjects;
            } else {
                /* return one only entry */
                $data = reset($result);
                $result = $this->_createEntityInstance($data);
            }
        }
        return $result;
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