<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Asset\Transfer\Init;

/**
 * Request to get initial data to start asset transfer operation.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Api\Request
{
    const CUSTOMER_ID = 'customerId';

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