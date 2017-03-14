<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Api\Transaction;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Data\Entity\Account as Acc;
use Praxigento\Accounting\Repo\Query\Trans\Get\Builder as Builder;

class Get
    implements \Praxigento\Accounting\Api\Transaction\GetInterface
{
    const BIND_ROOT_CUSTOMER_ID = 'rootCustId';
    /** @var \Praxigento\Core\Api\IAuthenticator */
    protected $authenticator;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $manObj;

    public function __construct(
        \Praxigento\Core\Api\IAuthenticator $authenticator,
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        $this->authenticator = $authenticator;
        $this->manObj = $manObj;
    }

    public function exec(\Praxigento\Accounting\Api\Transaction\Get\Request $data)
    {
        $result = new \Praxigento\Accounting\Api\Transaction\Get\Response();
        /* parse request, prepare query and fetch data */
        $qbuild = $this->getQueryBuilder();
        $bind = $this->prepareQueryParameters($data);
        $query = $qbuild->getSelectQuery();
        $query = $this->populateQuery($query, $bind);
        $rs = $this->performQuery($query, $bind);
        $rsData = new \Flancer32\Lib\Data($rs);
        $result->setData($rsData->get());
        return $result;
    }

    /**
     * @return \Praxigento\Core\Repo\Query\IBuilder
     */
    protected function getQueryBuilder()
    {
        $result = $this->manObj->get(\Praxigento\Accounting\Repo\Query\Trans\Get\Builder::class);
        return $result;
    }

    protected function performQuery($query, $bind)
    {
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query, (array)$bind->get());
        return $rs;
    }

    protected function populateQuery(
        \Magento\Framework\DB\Select $query,
        \Flancer32\Lib\Data $bind
    ) {
        $whereCrd = Builder::AS_ACC_CRD . '.' . Acc::ATTR_CUST_ID . '=:' . self::BIND_ROOT_CUSTOMER_ID;
        $whereDbt = Builder::AS_ACC_DBT . '.' . Acc::ATTR_CUST_ID . '=:' . self::BIND_ROOT_CUSTOMER_ID;
        $query->where("$whereCrd OR $whereDbt");
        return $query;
    }

    /**
     * @param \Flancer32\Lib\Data $data
     * @return array
     */
    protected function prepareQueryParameters(\Flancer32\Lib\Data $data)
    {
        $result = new \Flancer32\Lib\Data();
        if ($data instanceof \Praxigento\Accounting\Api\Transaction\Get\Request) {
            /** @var \Praxigento\Accounting\Api\Transaction\Get\Request $data */
            $rootCustId = $data->getRootCustId();
        }
        if (is_null($rootCustId)) {
            $user = $this->authenticator->getCurrentUserData();
            $rootCustId = $user->get(Cfg::E_CUSTOMER_A_ENTITY_ID);
        }
        $result->set(self::BIND_ROOT_CUSTOMER_ID, $rootCustId);
        return $result;
    }

}