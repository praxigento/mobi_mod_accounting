<?php
/**
 * Internal Service
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
    public function exec(\Praxigento\Accounting\Api\Service\Balance\Get\Turnover\Request $request);
}
