<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Asset\Transfer;

/**
 * Perform asset transfer operation.
 */
interface ProcessInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Asset\Transfer\Process\Request $request
     * @return \Praxigento\Accounting\Api\Asset\Transfer\Process\Response
     */
    public function exec(\Praxigento\Accounting\Api\Asset\Transfer\Process\Request $request);
}