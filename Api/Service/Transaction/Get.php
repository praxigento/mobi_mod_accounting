<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Transaction;

use Praxigento\Accounting\Api\Service\Transaction\Get\Request as ARequest;
use Praxigento\Accounting\Api\Service\Transaction\Get\Response as AResponse;

/**
 * Get list of transactions according to some searching criteria.
 */
interface Get
{
    /**
     * @param ARequest $request
     *
     * @return AResponse
     */
    public function exec($request);

}
