<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Service\Account;

/**
 * TODO: should we use this method or "\Praxigento\Accounting\Repo\Dao\Account::getSystemAccountIdByAssetCode"?
 * @deprecated use \Praxigento\Accounting\Repo\Dao\Account
 */
interface Get
{
    /**
     * @param \Praxigento\Accounting\Api\Service\Account\Get\Request $request
     * @return \Praxigento\Accounting\Api\Service\Account\Get\Response
     */
    public function exec($request);
}
