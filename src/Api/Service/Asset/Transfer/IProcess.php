<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Asset\Transfer;

/**
 * Perform asset transfer operation.
 */
interface IProcess
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Request $request
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Response
     */
    public function exec(\Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Request $request);
}