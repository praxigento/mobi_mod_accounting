<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Asset as Entity;
use Praxigento\Accounting\Repo\Entity\Type\IAsset;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Asset_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Asset */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Asset(
            $this->mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IAsset::class, $this->obj);
    }
}