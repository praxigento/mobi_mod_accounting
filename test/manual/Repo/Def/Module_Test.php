<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Repo\Def;

use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_ManualTest extends \Praxigento\Core\Lib\Test\BaseTestCase {


    public function test_getBalanceMaxDate() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $repo \Praxigento\Accounting\Lib\Repo\IModule */
        $repo = $obm->get('\Praxigento\Accounting\Lib\Repo\IModule');
        $data = $repo->getBalanceMaxDate(1);
        $this->assertTrue($data > 0);
    }

    public function test_getBalancesOnDate() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $repo \Praxigento\Accounting\Lib\Repo\IModule */
        $repo = $obm->get('\Praxigento\Accounting\Lib\Repo\IModule');
        $data = $repo->getBalancesOnDate(6, '20160220');
        $this->assertTrue($data > 0);
    }

    public function test_getRepresentative() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $repo \Praxigento\Accounting\Lib\Repo\IModule */
        $repo = $obm->get('\Praxigento\Accounting\Lib\Repo\IModule');
        $data = $repo->getRepresentativeCustomerId();
        $this->assertTrue($data > 0);
    }

    public function test_getTransactionMinDateApplied() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $repo \Praxigento\Accounting\Lib\Repo\IModule */
        $repo = $obm->get('\Praxigento\Accounting\Lib\Repo\IModule');
        $data = $repo->getTransactionMinDateApplied(6);
        $this->assertTrue($data > 0);
    }

    public function test_getTransactionsForPeriod() {
        $obm = Context::instance()->getObjectManager();
        /** @var  $repo \Praxigento\Accounting\Lib\Repo\IModule */
        $repo = $obm->get('\Praxigento\Accounting\Lib\Repo\IModule');
        $data = $repo->getTransactionsForPeriod(6, '2015-01-01 00:00:00', '2016-12-31 23:59:59');
        $this->assertTrue($data > 0);
    }

}