<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting;

include_once(__DIR__ . '/phpunit_bootstrap.php');

class Simple_Test extends \PHPUnit_Framework_TestCase {


    public function  test() {
        $lib = new \Praxigento\Accounting\Test\Integration\Simple();
        $lib->dataClean();
        $lib->dataInit();
        /* Perform assets validation */
        $lib->dataClean();
    }

}