<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset\Transfer;

/**
 * Response to process asset transfer between accounts.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\App\Api\Web\Response
{
    /**
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response\Data
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Transfer\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

}