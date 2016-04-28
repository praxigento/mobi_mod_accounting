<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Praxigento\Accounting\Data\Entity\Balance as EntityData;
use Praxigento\Core\Repo\IBaseRepo;

interface IBalance extends IBaseRepo
{
    /**
     * @param array|EntityData $data
     * @return int
     */
    public function create($data);

    /**
     * @param int $id
     * @return EntityData|bool
     */
    public function getById($id);
}