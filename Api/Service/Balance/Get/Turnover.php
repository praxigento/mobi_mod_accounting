<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Balance\Get;


interface Turnover
{
    /**
     * Get asset turnover for the given dates.
     *
     * @param \Praxigento\Accounting\Api\Service\Balance\Get\Turnover\Request $request
     * @return \Praxigento\Accounting\Api\Service\Balance\Get\Turnover\Response
     */
    public function exec($request);
}
