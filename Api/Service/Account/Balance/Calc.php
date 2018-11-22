<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance;

/**
 * Re-calculate daily balances by asset type or for given accounts only.
 */
interface Calc
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Response
     */
    public function exec($request);
}
