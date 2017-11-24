<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Account\Asset\Get\Response;

/**
 * Response to get asset data for customer.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Data
    extends \Praxigento\Core\Data
{
    const ITEMS = 'items';

    /**
     * @return \Praxigento\Accounting\Api\Data\Asset[]
     */
    public function getItems()
    {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Data\Asset[] $data
     */
    public function setItems($data)
    {
        parent::set(self::ITEMS, $data);
    }

}