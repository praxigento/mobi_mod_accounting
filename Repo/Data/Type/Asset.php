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
    const A_CURRENCY = 'currency';
    const A_IS_TRANSFERABLE = 'is_transferable';
    const ENTITY_NAME = 'prxgt_acc_type_asset';

    /** @return string Currency code */
    public function getCurrency()
    {
        $result = parent::get(self::A_CURRENCY);
        return $result;
    }

    /** @return bool */
    public function getIsTransferable()
    {
        $result = parent::get(self::A_IS_TRANSFERABLE);
        return $result;
    }

    /** @param string $data */
    public function setCurrency($data)
    {
        parent::set(self::A_CURRENCY, $data);
    }

    /** @param bool $data */
    public function setIsTransferable($data)
    {
        parent::set(self::A_IS_TRANSFERABLE, $data);
    }
}