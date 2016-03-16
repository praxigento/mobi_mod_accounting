<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api;

/**
 * Service to operate with 'account' entity in MOBI applications.
 * @api
 */
interface AccountInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}