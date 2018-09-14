<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Helper;

/**
 * System customer is a representative of the application owner in accounting.
 */
interface SysCustomer
{

    /**
     * Get customer's current balance for given asset.
     *
     * @param int $custId
     * @param string $assetTypeCode
     * @return int ID of the system customer.
     */
    public function getId();
}