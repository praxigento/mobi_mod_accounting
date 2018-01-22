<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Report\Daily;

/**
 * Request to get account turnover summary by day & transaction type (Odoo replication).
 *
 * (Define getters explicitly to use with Swagger tool)
 */
class Request
    extends \Praxigento\Core\Data
{
    const PERIOD = 'period';

    /**
     * @return \Praxigento\Accounting\Service\Account\Report\Daily\Request\Period
     */
    public function getPeriod()
    {
        $result = parent::get(self::PERIOD);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Service\Account\Report\Daily\Request\Period $data
     */
    public function setPeriod($data)
    {
        parent::set(self::PERIOD, $data);
    }
}