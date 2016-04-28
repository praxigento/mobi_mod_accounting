<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Operation as Entity;
use Praxigento\Accounting\Repo\Entity\Type\IOperation;
use Praxigento\Core\Repo\Def\Type as BaseType;

class Operation extends BaseType implements IOperation
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}