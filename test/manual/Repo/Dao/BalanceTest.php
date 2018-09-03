<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Accounting\Repo\Dao;

use Praxigento\Accounting\Repo\Data\Balance as Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class BalanceTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    public function test_maxDecimalValue()
    {
        $entity = new Entity();
        $entity->setAccountId(4);
        $entity->setDate('YYYYMMD1');
        $entity->setBalanceOpen(0);
        $entity->setBalanceClose(-111111111111.4321);
        $entity->setTotalCredit(0);
        $entity->setTotalDebit(0);

        /** @var \Praxigento\Accounting\Repo\Dao\Balance $dao */
        $dao = $this->manObj->get(\Praxigento\Accounting\Repo\Dao\Balance::class);
        $dao->create($entity);

        $this->assertEquals(1, 1);
    }
}