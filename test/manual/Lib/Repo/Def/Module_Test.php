<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Repo\Def;

use Magento\Framework\App\ObjectManager;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Module_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Lib\Repo\Def\Module */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Lib\Repo\Def\Module::class);
    }

    public function test_getBalanceMaxDate()
    {
        $data = $this->_obj->getBalanceMaxDate(1);
        $this->assertTrue($data > 0);
    }

    public function test_getBalancesOnDate()
    {
        $data = $this->_obj->getBalancesOnDate(6, '20160220');
        $this->assertTrue($data > 0);
    }

    public function test_getRepresentative()
    {
        $data = $this->_obj->getRepresentativeCustomerId();
        $this->assertTrue($data > 0);
    }

    public function test_getTransactionMinDateApplied()
    {
        $data = $this->_obj->getTransactionMinDateApplied(6);
        $this->assertTrue($data > 0);
    }

    public function test_getTransactionsForPeriod()
    {
        $data = $this->_obj->getTransactionsForPeriod(6, '2015-01-01 00:00:00', '2016-12-31 23:59:59');
        $this->assertTrue($data > 0);
    }

}