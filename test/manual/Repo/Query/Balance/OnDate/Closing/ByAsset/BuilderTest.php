<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

use Praxigento\Santegra\Cli\Migrate\Z\Config as CfgZ;

class BuilderTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    /**
     * Compare mage & backend downlines for 2018/02
     */
    public function test_build()
    {
        /** @var \Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder $obj */
        $obj = $this->manObj->get(\Praxigento\Accounting\Repo\Query\Balance\OnDate\Closing\ByAsset\Builder::class);
        $query = $obj->build();
        $this->assertNotNull('done');
    }

}