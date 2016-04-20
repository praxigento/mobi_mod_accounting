<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Type\Def;

use Magento\Framework\App\ObjectManager;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Asset_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Def\Asset */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Accounting\Repo\Entity\Type\Def\Asset::class);
    }

    public function test_getIdByCode()
    {
        $data = $this->_obj->getIdByCode('pv');
        $this->assertTrue($data > 0);
    }

}