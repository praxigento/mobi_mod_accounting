<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Accounting\Cli\Balance;

use Praxigento\Accounting\Service\Account\Balance\Validate\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Validate\Response as AResponse;

/**
 * Validate current balances for customers accounts.
 */
class Validate
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Accounting\Service\Account\Balance\Validate */
    private $servValidate;

    public function __construct(
        \Praxigento\Accounting\Service\Account\Balance\Validate $servValidate
    ) {
        parent::__construct(
            'prxgt:acc:balance:validate',
            'Validate current balances for customers accounts.'
        );
        $this->servValidate = $servValidate;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $req = new ARequest();
        $this->servValidate->exec($req);
    }
}