<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;

/**
 * Perform assets transfer.
 */
class Process
    extends \Praxigento\Core\App\Action\Back\Api\Base
{
    /** @var \Praxigento\Accounting\Api\Service\Asset\Transfer\IProcess */
    private $callProcess;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Accounting\Api\Service\Asset\Transfer\IProcess $callProcess
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger);
        $this->callProcess = $callProcess;
    }

    protected function getInDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Adminhtml\Customer\Accounting\Process\Request::class;
    }

    protected function getOutDataType(): string
    {
        return \Praxigento\Accounting\Api\Ctrl\Adminhtml\Customer\Accounting\Process\Response::class;
    }

    protected function process($request)
    {
        /* define local working data */
        assert($request instanceof \Praxigento\Accounting\Api\Service\Asset\Transfer\Process\Request);

        /* perform processing */
        $userId = $this->_auth->getUser()->getId();
        $request->setUserId($userId);
        $result = $this->callProcess->exec($request);

        /* compose result */
        return $result;
    }

}