<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Flancer32\Lib\DataObject;
use Praxigento\Core\Repo\IBaseRepo;

interface IAccount extends IBaseRepo
{
    /**
     * @param array|DataObject $data
     * @return array
     */
    public function create($data);

    public function getByCustomerId($customerId, $assetTypeId);

    /**
     * @param int $id
     * @return array
     */
    public function getById($id);

    public function updateBalance($accountId, $delta);
}