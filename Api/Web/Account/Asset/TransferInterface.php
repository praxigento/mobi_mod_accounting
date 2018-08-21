<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset;

/**
 * Web service to transfer assets between accounts (customer or system) from frontend (by customer).
 * See "\Praxigento\Accounting\Controller\Adminhtml\Asset\Transfer" for adminhtml (by backend operator).
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