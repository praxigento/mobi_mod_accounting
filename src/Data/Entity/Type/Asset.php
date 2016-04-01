<?php
/**
 * Account assets type codifier.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Entity\Type;

use Praxigento\Core\Data\Entity\Type\Base as TypeBase;

class Asset extends TypeBase
{
    const ENTITY_NAME = 'prxgt_acc_type_asset';

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }
}