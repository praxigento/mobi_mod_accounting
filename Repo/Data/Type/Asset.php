<?php
/**
 * Account assets type codifier.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data\Type;

class Asset
    extends \Praxigento\Core\App\Repo\Data\Entity\Type\Base
{
    const A_IS_VISIBLE = 'is_visible';
    const ENTITY_NAME = 'prxgt_acc_type_asset';

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        $result = parent::get(self::A_IS_VISIBLE);
        return $result;
    }

    /**
     * @param bool $data
     */
    public function setIsVisible($data)
    {
        parent::set(self::A_IS_VISIBLE, $data);
    }
}