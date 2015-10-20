<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Core\Lib\Setup\Schema\Base as SchemaBase;

class Schema extends SchemaBase {

    /**
     * InstallSchema constructor.
     */
    public function __construct() {
        parent::__construct('\Praxigento\Accounting\Lib\Setup\Schema');
    }
}