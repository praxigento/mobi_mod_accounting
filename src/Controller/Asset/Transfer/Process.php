<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Asset\Transfer;


/**
 * Web API action to process assets transfer for the customer.
 */
class Process
    extends \Praxigento\Core\App\Action\Front\Api\Base
{
    /** @var \Praxigento\Accounting\Api\Service\Asset\Transfer\Process */
    private $callProcess;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\App\Logger\App $logger,
        \Praxigento\Core\App\Web\IAuthenticator $authenticator,
        \Praxigento\Accounting\Api\Service\Asset\Transfer\Process $callProcess
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger, $authenticator);
        $this->callProcess = $callProcess;
    }

    protected function getInDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Account\Asset\Transfer\Process\Request::class;
    }

    protected function getOutDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Account\Asset\Transfer\Process\Response::class;
    }

    protected function process($request)
    {
        /* define local working data */
        assert($request instanceof \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Request);
        $amount = $request->getAmount();
        $customerId = $request->getCustomerId();

        /* perform processing */
        $customerId = $this->authenticator->getCurrentCustomerId($customerId);
        $request->setCustomerId($customerId);
        $request->setIsDirect(false);
        $request->setAmount(abs($amount));
        $result = $this->callProcess->exec($request);

        /* compose result */
        return $result;
    }


}