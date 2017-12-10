<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Rest\Transaction\Get;

/**
 * Request to get all transactions according to some selection conditions (search criteria).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\App\Web\Request\WithCond
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