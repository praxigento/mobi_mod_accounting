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
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Validate */
    private $servValidate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
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
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
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
        $this->conn->beginTransaction();
        try {
            $req = new ARequest();
            $this->servValidate->exec($req);

            $this->conn->commit();
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
            $this->conn->rollBack();
        }
        $msg = "Command '" . $this->getName() . "' is completed.";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);
    }
}