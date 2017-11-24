<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Rest\Transaction\Get;

/**
 * Response to get all transactions according to some selection conditions (search criteria).
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Accounting\Api\Rest\Transaction\Get\Response\Entry[]
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
     * @param \Praxigento\Accounting\Api\Rest\Transaction\Get\Response\Entry[] $data
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