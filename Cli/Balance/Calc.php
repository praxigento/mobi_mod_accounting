<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Cli\Balance;

/**
 * Re-calculate accounts balances.
 */
class Calc
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_DAYS_DEF = '2';
    const OPT_DAYS_NAME = 'days';
    const OPT_DAYS_SHORTCUT = 'd';

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Praxigento\Accounting\Api\Service\Account\Balance\Calc */
    private $servBalance;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Api\Service\Account\Balance\Calc $servBalance
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:calc',
            'Re-calculate accounts balances (reset daily balances up to $days).'
        );
        $this->logger = $logger;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->servBalance = $servBalance;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_DAYS_NAME,
            self::OPT_DAYS_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Days to re-calc balances ("-d 7" - reset balances for the last week).',
            self::OPT_DAYS_DEF
        );

    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* get CLI input parameters */
        $days = $input->getOption(self::OPT_DAYS_NAME);
        $msg = "Command '" . $this->getName() . "' (days to reset: $days):";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);

        /* wrap all DB operations with DB transaction */
        $this->conn->beginTransaction();
        try {
            $req = new \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request();
            $req->setDaysToReset($days);
            $this->servBalance->exec($req);
            $this->conn->commit();
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '<info>');
            $this->conn->rollBack();
        }
        $msg = "Command '" . $this->getName() . "' is completed.";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);
    }
}