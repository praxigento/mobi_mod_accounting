<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction\Get;

/**
 * Request to get all transactions according to some selection conditions (search criteria).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{
    /**
     * @return \Praxigento\Core\Api\Request\Part\Conditions|null
     */
    public function getConditions()
    {
        $result = parent::getConditions();
        return $result;
    }

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
     * @return \Magento\Framework\Api\SearchCriteria|null
     */
    public function getSearchCriteria()
    {
        $result = parent::getSearchCriteria();
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