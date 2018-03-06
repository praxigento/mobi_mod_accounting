<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset;

/**
 * Process asset transfer between accounts (customer or representative).
 */
interface TransferInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Request $request
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}