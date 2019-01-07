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
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Validate */
    private $servValidate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Service\Account\Balance\Validate $servValidate
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:validate',
            'Validate current balances for customers accounts.'
        );
        $this->logger = $logger;
        $this->manTrans = $manTrans;
        $this->servValidate = $servValidate;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* get CLI input parameters */
        $msg = "Command '" . $this->getName();
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);

        /* wrap all DB operations with DB transaction */
        $def = $this->manTrans->begin();

        $req = new ARequest();
        /** @var AResponse $resp */
        $resp = $this->servValidate->exec($req);

        $this->manTrans->commit($def);

        $msg = "Command '" . $this->getName() . "' is completed.";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);
    }
}