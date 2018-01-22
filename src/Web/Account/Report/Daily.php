<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Web\Account\Report;

use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Request as ARequest;
use Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response as AResponse;

/**
 * API adapter for internal service to get account turnover summary by day & transaction type (Odoo replication).
 */
class Daily
    implements \Praxigento\Accounting\Api\Web\Account\Report\DailyInterface
{

    private $servReportDaily;

    public function __construct(
        \Praxigento\Accounting\Service\Account\Report\Daily $servReportDaily
    ) {
        $this->servReportDaily = $servReportDaily;
    }


    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();

        /** perform processing */
        $resp = $this->servReportDaily->exec($data);

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

}