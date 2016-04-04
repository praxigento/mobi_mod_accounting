<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Asset as EntityAsset;
use Praxigento\Accounting\Repo\Entity\Type\IAsset;
use Praxigento\Core\Repo\Def\Type as BaseType;

class Asset extends BaseType implements IAsset
{

    protected function _getEntityName()
    {
        return EntityAsset::ENTITY_NAME;
    }
}