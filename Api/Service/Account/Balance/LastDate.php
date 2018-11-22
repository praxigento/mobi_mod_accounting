<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance;

/**
 * Get the last date for the balance (by asset type or by account).
 */
interface LastDate
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Response
     */
    public function exec($request);
}
