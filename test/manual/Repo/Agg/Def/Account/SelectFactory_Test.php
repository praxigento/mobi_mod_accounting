<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SelectFactory_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  SelectFactory */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = \Magento\Framework\App\ObjectManager::getInstance()->create(SelectFactory::class);
    }

    public function test_getQueryToSelect()
    {
        $res = $this->_obj->getQueryToSelect();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class);
        $conn = $resource->getConnection();
        $data = $conn->fetchAll($res);
    }

    public function test_getQueryToSelectCount()
    {
        $res = $this->_obj->getQueryToSelectCount();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class);
        $conn = $resource->getConnection();
        $data = $conn->fetchOne($res);
    }

}