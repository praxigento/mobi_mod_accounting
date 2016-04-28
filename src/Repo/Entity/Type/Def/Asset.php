<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Accounting\Data\Entity\Type\Asset as Entity;
use Praxigento\Accounting\Repo\Entity\Type\IAsset as IEntityRepo;
use Praxigento\Core\Repo\Def\Type as BaseType;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;

class Asset extends BaseType implements IEntityRepo
{

    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }
}