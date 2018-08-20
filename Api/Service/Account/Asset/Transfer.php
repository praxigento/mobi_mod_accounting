<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Account\Asset;


interface Transfer
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Response
     */
    public function exec($request);
}