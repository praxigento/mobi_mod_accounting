<?php
/**
 * Internal Service
 */

namespace Praxigento\Accounting\Api\Service\Account;


interface Get
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Get\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Get\Response
     */
    public function exec($request);
}
