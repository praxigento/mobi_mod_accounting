<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Praxigento\Accounting\Data\Entity\Account as EntityData;
use Praxigento\Core\Repo\IBaseRepo;

interface IAccount extends IBaseRepo
{
    /**
     * @param array|EntityData $data
     * @return int
     */
    public function create($data);

    /**
     * @param $customerId
     * @param $assetTypeId
     * @return null|EntityData
     */
    public function getByCustomerId($customerId, $assetTypeId);

    /**
     * @param int $id
     * @return EntityData|bool
     */
    public function getById($id);

    public function updateBalance($accountId, $delta);
}