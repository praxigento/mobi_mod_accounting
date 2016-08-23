<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Balance as Entity;
use Praxigento\Accounting\Repo\Entity\IBalance;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Balance_UnitTest
    extends \Praxigento\Core\Test\BaseRepoEntityCase
{
    /** @var  Balance */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = new Balance(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IBalance::class, $this->obj);
    }

}