<?php
/**
 * Repository to operate with 'Account" aggregate in this module.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;


class Account
    extends \Praxigento\Core\Repo\Def\Crud
    implements \Praxigento\Accounting\Repo\Agg\IAccount
{

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var Account\SelectFactory */
    protected $_factorySelect;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Warehouse\Repo\Entity\IWarehouse $repoEntityWarehouse,
        Account\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoGeneric = $repoGeneric;
        $this->_factorySelect = $factorySelect;
    }

    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getQueryToSelect();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getQueryToSelectCount();
        return $result;
    }

}