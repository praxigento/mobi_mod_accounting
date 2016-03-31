<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Balance_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase
{

    public function test_pk()
    {
        $entity = new Balance();
        $pk = $entity->getPrimaryKeyAttrs();
        $this->assertTrue(is_array($pk));
        $this->assertEquals(Balance::ATTR_ACCOUNT_ID, $pk[0]);
        $this->assertEquals(Balance::ATTR_DATE, $pk[1]);

    }
}