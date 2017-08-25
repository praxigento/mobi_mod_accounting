<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Accounting\Repo\Entity\Data\Operation as Entity;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;

class Operation extends BaseEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    )
    {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param array|\Praxigento\Accounting\Repo\Entity\Data\Operation $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

}