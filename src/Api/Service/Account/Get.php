<?php
/**
 * Internal Service
 */

namespace Praxigento\Accounting\Api\Service\Account;


interface Get
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Get\Request $req
     * @return \Praxigento\Accounting\Api\Service\Account\Get\Response
     */
    public function exec($req);
}
