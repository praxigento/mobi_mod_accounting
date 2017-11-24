<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;

/**
 * Get customer & assets data to initialize modal slider to perform assets transfer.
 */
class Init
    extends \Magento\Backend\App\Action
{
    const VAR_CUSTOMER_ID = 'customerId';
    /** @var \Praxigento\Accounting\Api\Service\Asset\Transfer\IInit */
    private $callInit;
    private $outputProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Accounting\Api\Service\Asset\Transfer\IInit $callInit
    )
    {
        parent::__construct($context);
        $this->outputProcessor = $outputProcessor;
        $this->callInit = $callInit;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $customerId = $this->getRequest()->getParam(self::VAR_CUSTOMER_ID);

        /* TODO: add ACL */
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Request();
        $req->setCustomerId($customerId);
        $resp = $this->callInit->exec($req);

        /* convert service data object into JSON */
        $className = \Praxigento\Accounting\Api\Service\Asset\Transfer\IInit::class;
        $methodName = 'exec';
        $stdResp = $this->outputProcessor->process($resp, $className, $methodName);

        /* extract data part from response */
        $data = $stdResp[\Praxigento\Core\Api\Response::ATTR_DATA];
        $resultPage->setData($data);
        return $resultPage;
    }
}