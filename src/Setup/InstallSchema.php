<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base {

    /**
     * InstallSchema constructor.
     */
    public function __construct() {
        parent::__construct('Praxigento\Accounting\Lib\Setup\Schema');
    }
}