<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Accounting\Data\Entity\Transaction as Entity;
use Praxigento\Accounting\Repo\Entity\ITransaction as IEntityRepo;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;

class Transaction extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }
}