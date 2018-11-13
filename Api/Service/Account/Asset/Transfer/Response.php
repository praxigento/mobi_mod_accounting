<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Account\Asset\Transfer;

/**
 * Response to process asset transfer between accounts.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const AMOUNT = 'amount';
    const OPER_ID = 'operId';

    /**
     * @return float
     */
    public function getAmount()
    {
        $result = parent::get(self::AMOUNT);
        return $result;
    }

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
     * @param float $data
     * @return void
     */
    public function setAmount($data)
    {
        parent::set(self::AMOUNT, $data);
    }

    /**
     *
     * @param int $data
     * @return void
     */
    public function setOperId($data)
    {
        parent::set(self::OPER_ID, $data);
    }
}