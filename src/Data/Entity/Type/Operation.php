<?php
/**
 * Account operations type codifier.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Entity\Type;

use Praxigento\Core\Data\Entity\Type\Base as TypeBase;

class Operation extends TypeBase
{
    const ENTITY_NAME = 'prxgt_acc_type_operation';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }
}