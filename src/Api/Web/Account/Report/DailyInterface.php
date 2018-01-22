<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Report;

/**
 * Get account turnover summary by day & transaction type (Odoo replication).
 */
interface DailyInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Report\Daily\Request $request
     * @return \Praxigento\Accounting\Api\Web\Account\Report\Daily\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}