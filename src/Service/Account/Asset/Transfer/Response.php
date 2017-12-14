<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset\Transfer;

/**
 * Response to process asset transfer between accounts.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Data
{
    const OPER_ID = 'operId';

    /**
     * @return int
     */
    public function getOperId()
    {
        $result = parent::get(self::OPER_ID);
        return $result;
    }

    /**
     *
     * @param int $data
     */
    public function setOperId($data)
    {
        parent::set(self::OPER_ID, $data);
    }
}