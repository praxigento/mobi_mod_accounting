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
    const VAR_CUSTOMER_ID = 'customer_id';
    /** @var \Praxigento\Accounting\Api\Asset\Transfer\InitInterface */
    private $callInit;
    /** @var \Magento\Framework\Serialize\Serializer\Json */
    private $serializer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Praxigento\Accounting\Api\Asset\Transfer\InitInterface $callInit
    )
    {
        parent::__construct($context);
        $this->serializer = $serializer;
        $this->callInit = $callInit;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $customerId = $this->getRequest()->getParam(self::VAR_CUSTOMER_ID);
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Accounting\Api\Asset\Transfer\Init\Request();
        $req->setCustomerId($customerId);
        $resp = $this->callInit->exec($req);
        $data = $resp->getData();
        $json = $this->serializer->serialize($data);
        $resultPage->setData($data);
        return $resultPage;
    }
}