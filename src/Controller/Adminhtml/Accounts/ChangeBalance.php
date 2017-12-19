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
    /** @var \Praxigento\Accounting\Service\Account\Balance\Change */
    private $changeBalance;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Accounting\Service\Account\Balance\Change $callBalance
    )
    {
        parent::__construct($context);
        $this->changeBalance = $callBalance;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $value = $this->getRequest()->getParam(self::VAR_CHANGE_VALUE);
        $accountId = $this->getRequest()->getParam(self::VAR_ACCOUNT_ID);
        $userId = $this->_auth->getUser()->getId();
        $req = new \Praxigento\Accounting\Service\Account\Balance\Change\Request();
        $req->setCustomerAccountId($accountId);
        $req->setChangeValue($value);
        $req->setAdminUserId($userId);
        $resp = $this->changeBalance->exec($req);
        $resultPage->setData(['error' => !$resp->isSucceed()]);
        return $resultPage;
    }
}