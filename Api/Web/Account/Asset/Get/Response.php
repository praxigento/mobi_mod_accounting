<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Web\Account\Asset\Get;

/**
 * Response to get initial data to start asset transfer operation.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    /**
     * @return \Praxigento\Accounting\Api\Web\Account\Asset\Get\Response\Data
     */
    public function getData()
    {
        $result = parent::get(self::A_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Web\Account\Asset\Get\Response\Data $data
     * @return void
     */
    public function setData($data)
    {
        parent::set(self::A_DATA, $data);
    }

}