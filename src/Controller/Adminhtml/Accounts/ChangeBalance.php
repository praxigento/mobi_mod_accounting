<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Accounts;

/**
 * This controller should return JSON as result of the processing.
 */
class ChangeBalance
    extends \Magento\Backend\App\Action
{
    const VAR_ACCOUNT_ID = 'accountId';
    const VAR_CHANGE_VALUE = 'changeValue';
    /** @var \Praxigento\Accounting\Service\IBalance */
    protected $_callBalance;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Accounting\Service\IBalance $callBalance
    ) {
        parent::__construct($context);
        $this->_callBalance = $callBalance;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $value = $this->getRequest()->getParam(self::VAR_CHANGE_VALUE);
        $accountId = $this->getRequest()->getParam(self::VAR_ACCOUNT_ID);
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Accounting\Service\Balance\Request\Change();
        $req->setCustomerAccountId($accountId);
        $req->setChangeValue($value);
        $req->setAdminUserId($userId);
        $resp = $this->_callBalance->change($req);
        $resultPage->setData(['error' => !$resp->isSucceed()]);
        return $resultPage;
    }
}