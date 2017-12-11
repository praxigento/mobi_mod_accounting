<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;

use Praxigento\Accounting\Config as Cfg;

/**
 * Perform assets transfer.
 */
class Process
    extends \Praxigento\Core\App\Action\Back\Api\Base
{
    const ADMIN_RESOURCE = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS;

    /** @var \Praxigento\Accounting\Api\Service\Asset\Transfer\Process */
    private $callProcess;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Core\App\Logger\App $logger,
        \Praxigento\Accounting\Api\Service\Asset\Transfer\Process $callProcess
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