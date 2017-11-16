<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Asset\Transfer\Init\Response;
class Data
    extends \Praxigento\Core\Data
{
    const CUSTOMER_ID = 'customer_id';

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::CUSTOMER_ID, $data);
    }
}