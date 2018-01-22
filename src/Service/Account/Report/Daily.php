<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Report;

use Praxigento\Accounting\Service\Account\Report\Daily\Request as ARequest;
use Praxigento\Accounting\Service\Account\Report\Daily\Response as AResponse;

/**
 * Module level service to get account turnover summary by day & transaction type (Odoo replication).
 */
class Daily
{
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $period = $request->getPeriod();
        $from = $period->getFrom();
        $to = $period->getTo();

        /** perform processing */


        /** compose result */
        $result = new AResponse();
        return $result;
    }
}