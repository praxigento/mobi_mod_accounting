<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset\Get;

/**
 * Request to get asset data for a customer (available asset types, existing accounts & balances).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Data
{
    const CUSTOMER_ID = 'customerId';

    /**
     * @return int
     */
    public function getCustomerId() {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /**
     * @param int $data
     * @return void
     */
    public function setCustomerId($data) {
        parent::set(self::CUSTOMER_ID, $data);
    }
}