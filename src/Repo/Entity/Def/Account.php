<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Accounting\Data\Entity\Account as Entity;
use Praxigento\Accounting\Repo\Entity\IAccount as IEntityRepo;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Core\Repo\Query\Expression;

class Account extends BaseEntityRepo implements IEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
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

    /** @inheritdoc */
    public function updateBalance($accountId, $delta)
    {
        if ($delta < 0) {
            $exp = '(`' . Entity::ATTR_BALANCE . '`-' . abs($delta) . ')';
        } else {
            $exp = '(`' . Entity::ATTR_BALANCE . '`+' . abs($delta) . ')';
        }
        $exp = new Expression($exp);
        $bind = [Entity::ATTR_BALANCE => $exp];
        $rowsUpdated = $this->updateById($accountId, $bind);
        return $rowsUpdated;
    }
}