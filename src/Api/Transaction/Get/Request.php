<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction\Get;

/**
 * Request to get transactions for the customer.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{
    /**
     * Root Customer ID for development purposes.
     *
     * @return int
     */
    public function getRootCustId()
    {
        $result = parent::getRootCustId();
        return $result;
    }

    /**
     * Root Customer ID for development purposes.
     *
     * @param int $data
     */
    public function setRootCustId($data)
    {
        parent::setRootCustId($data);
    }

}