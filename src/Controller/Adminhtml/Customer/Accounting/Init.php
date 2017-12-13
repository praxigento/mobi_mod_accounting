<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;


use Praxigento\Accounting\Api\Ctrl\Adminhtml\Customer\Accounting\Init\Request as ARequest;
use Praxigento\Accounting\Api\Ctrl\Adminhtml\Customer\Accounting\Init\Response as AResponse;
use Praxigento\Accounting\Config as Cfg;

/**
 * Get customer & assets data to initialize modal slider to perform assets transfer.
 */
class Init
    extends \Praxigento\Core\App\Action\Back\Api\Base
{
    const ADMIN_RESOURCE = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS;

    /** @var \Praxigento\Accounting\Service\Asset\Transfer\Init */
    private $callInit;

    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Accounting\Service\Asset\Transfer\Init $callInit
    )
    {
        parent::__construct($context, $inputProcessor, $outputProcessor, $logger);
        $this->callInit = $callInit;
    }

    protected function getInDataType(): string
    {
        return ARequest::class;
    }

    protected function getOutDataType(): string
    {
        return AResponse::class;
    }

    protected function process($request)
    {
        /* define local working data */
        assert($request instanceof ARequest);

        /* perform processing */
        $result = $this->callInit->exec($request);

        /* compose result */
        return $result;

    }
}