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
     * Get ID of the system customer.
     *
     * @return int ID of the system customer.
     */
    public function getId();
}