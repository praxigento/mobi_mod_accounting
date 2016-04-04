<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Operation as EntityOperation;
use Praxigento\Accounting\Repo\Entity\Type\IOperation;
use Praxigento\Core\Repo\Def\Type as BaseType;

class Operation extends BaseType implements IOperation
{

    protected function _getEntityName()
    {
        return EntityOperation::ENTITY_NAME;
    }
}