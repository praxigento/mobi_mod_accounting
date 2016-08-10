<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Praxigento\Accounting\Data\Entity\Transaction as EntityData;
use Praxigento\Core\Repo\ICrud;


interface ITransaction extends ICrud
{
    /**
     * @param array|EntityData $data
     * @return int
     */
    public function create($data);
}