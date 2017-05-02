<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance;


interface IGet
{

    /**
     * Get asset balances on the given dates.
     *
     * @param \Praxigento\Accounting\Service\Balance\Get\Request $request
     *
     * @return \Praxigento\Accounting\Service\Balance\Get\Response
     */
    public function exec(\Praxigento\Accounting\Service\Balance\Get\Request $request);

}