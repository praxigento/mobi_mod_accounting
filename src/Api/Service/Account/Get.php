<?php
/**
 * Internal Service
 */

namespace Praxigento\Accounting\Api\Service\Account;


interface Get
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Get\Request $data
     * @return \Praxigento\Accounting\Api\Service\Account\Get\Response
     */
    public function exec(\Praxigento\Accounting\Api\Service\Account\Get\Request $data);
}
