<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Asset\Transfer\Process;

/**
 * Response to get initial data to start asset transfer operation.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Response\Data
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        $result = parent::getRequest();
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

    /**
     *
     * @param string $data
     */
    public function setRequest($data)
    {
        parent::setRequest($data);
    }

}