<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset\Transfer;

/**
 * Load initial data for asset transfer dialogs.
 */
interface InitInterface
{
    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Init\Request $request
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Init\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}