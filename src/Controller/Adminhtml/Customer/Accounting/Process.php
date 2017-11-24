<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting;

/**
 * Perform assets transfer.
 */
class Process
    extends \Magento\Backend\App\Action
{
    const VAR_AMOUNT = 'amount';
    const VAR_ASSET_ID = 'assetId';
    const VAR_COUNTER_PARTY_ID = 'counterPartyId';
    const VAR_CUSTOMER_ID = 'customerId';
    const VAR_IS_DIRECT = 'isDirect';

    /** @var \Praxigento\Accounting\Api\Asset\Transfer\ProcessInterface */
    private $callProcess;

    private $outputProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Webapi\ServiceOutputProcessor $outputProcessor,
        \Praxigento\Accounting\Api\Asset\Transfer\ProcessInterface $callProcess
    )
    {
        parent::__construct($context);
        $this->outputProcessor = $outputProcessor;
        $this->callProcess = $callProcess;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $amount = (float)$this->getRequest()->getParam(self::VAR_AMOUNT);
        $assetId = (int)$this->getRequest()->getParam(self::VAR_ASSET_ID);
        $counterPartyId = (int)$this->getRequest()->getParam(self::VAR_COUNTER_PARTY_ID);
        $customerId = (int)$this->getRequest()->getParam(self::VAR_CUSTOMER_ID);
        /* convert string 'true' or 'false' to boolean */
        $isDirect = $this->getRequest()->getParam(self::VAR_IS_DIRECT);
        $isDirect = filter_var($isDirect, FILTER_VALIDATE_BOOLEAN);

        /* TODO: add ACL */
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Accounting\Api\Asset\Transfer\Process\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetId);
        $req->setCounterPartyId($counterPartyId);
        $req->setCustomerId($customerId);
        $req->setIsDirect($isDirect);
        $req->setUserId($userId);
        $resp = $this->callProcess->exec($req);

        /* convert service data object into JSON */
        $className = \Praxigento\Accounting\Api\Asset\Transfer\ProcessInterface::class;
        $methodName = 'exec';
        $stdResp = $this->outputProcessor->process($resp, $className, $methodName);

        /* extract data part from response */
        $data = $stdResp[\Praxigento\Core\Api\Response::ATTR_DATA];
        $resultPage->setData($data);
        return $resultPage;
    }
}