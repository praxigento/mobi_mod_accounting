<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Asset\Transfer;

/**
 * Get initial data to start asset transfer operation on UI.
 */
interface IInit
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Request $data
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response
     */
    public function exec(\Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Request $data);
}