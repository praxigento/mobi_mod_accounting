<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service;

use Praxigento\Accounting\Api\Service\Operation\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Response as AResponse;

/**
 * Create new operation (with nested transactions & balances changes).
 */
interface Operation
{
    /**
     * @param ARequest $request
     *
     * @return AResponse
     */
    public function exec($request);

}
