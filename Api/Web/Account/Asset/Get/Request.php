<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset\Get;

/**
 * Request to get asset data for a customer.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Api\App\Web\Request
{
    /**
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Get\Request\Data
     */
    public function getData() {
        $result = parent::get(self::DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Get\Request\Data $data
     */
    public function setData($data) {
        parent::set(self::DATA, $data);
    }

}