<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Ctrl\Asset\Transfer;

/**
 * Perform asset transfer operation.
 */
interface ProcessInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Request $request
     * @return \Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Response
     */
    public function exec(\Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Request $request);
}