<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Ctrl\Asset\Transfer;

/**
 * Get initial data to start asset transfer operation on UI.
 */
interface InitInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Init\Request $data
     * @return \Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Init\Response
     */
    public function exec(\Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Init\Request $data);
}