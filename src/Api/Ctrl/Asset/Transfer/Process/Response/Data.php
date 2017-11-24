<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Ctrl\Asset\Transfer\Process\Response;

class Data
    extends \Praxigento\Core\Data
{
    const OPER_ID = 'oper_id';

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