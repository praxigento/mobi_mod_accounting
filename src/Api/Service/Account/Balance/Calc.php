<?php
/**
 * Internal Service
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance;


/**
 * Calculate accounts balances (historical data).
 */
interface Calc
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request $req
     * @return \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Response
     */
    public function exec($req);
}
