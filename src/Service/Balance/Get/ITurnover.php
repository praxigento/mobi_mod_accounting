<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance\Get;


interface ITurnover
{

    /**
     * Get asset turnover for the given dates.
     *
     * @param \Praxigento\Accounting\Service\Balance\Get\Turnover\Request $request
     *
     * @return \Praxigento\Accounting\Service\Balance\Get\Turnover\Response
     */
    public function exec(\Praxigento\Accounting\Service\Balance\Get\Turnover\Request $request);

}