<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Report\Daily;

/**
 * Request to get account turnover summary by day & transaction type (for Odoo).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\App\Api\Web\Request
{
    /**
     * @return \Praxigento\Accounting\Api\Web\Account\Report\Daily\Request\Data
     */
    public function getData() {
        $result = parent::get(self::DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Report\Daily\Request\Data $data
     */
    public function setData($data) {
        parent::set(self::DATA, $data);
    }

}