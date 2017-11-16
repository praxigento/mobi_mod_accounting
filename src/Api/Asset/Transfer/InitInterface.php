<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Asset\Transfer;

/**
 * Get initial data to start asset transfer operation.
 */
interface InitInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Asset\Transfer\Init\Request $data
     * @return \Praxigento\Accounting\Api\Asset\Transfer\Init\Response
     */
    public function exec(\Praxigento\Accounting\Api\Asset\Transfer\Init\Request $data);
}