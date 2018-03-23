<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Accounting\Repo\Data\Operation as Entity;
use Praxigento\Core\App\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\App\Repo\IGeneric as IRepoGeneric;

class Operation extends BaseEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $daoGeneric
    )
    {
        parent::__construct($resource, $daoGeneric, Entity::class);
    }

    /**
     * @param array|\Praxigento\Accounting\Repo\Data\Operation $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

}