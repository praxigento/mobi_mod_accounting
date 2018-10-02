<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Operation;

use Praxigento\Accounting\Api\Service\Operation\Create\Request as ARequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Response as AResponse;

/**
 * Create new operation (with nested transactions & balances changes).
 */
interface Create
{
    /**
     * @param ARequest $request
     *
     * @return AResponse
     */
    public function exec($request);

}
