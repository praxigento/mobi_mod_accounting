<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset;

/**
 * Get asset data for a customer (available asset types, existing accounts & balances).
 */
interface GetInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Get\Request $request
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Get\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}