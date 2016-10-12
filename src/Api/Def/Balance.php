<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Def;

/**
 * Class Balance
 * @package Praxigento\Accounting\Api\Def
 */
class Balance
    implements \Praxigento\Accounting\Api\BalanceInterface
{
    /** @var \Praxigento\Accounting\Service\IBalance */
    protected $_callBalance;
    /** @var \Magento\Backend\Model\Auth\Session */
    protected $_authSession;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Praxigento\Accounting\Service\IBalance $callBalance
    ) {
        $this->_callBalance = $callBalance;
        $this->_authSession = $authSession;
    }

    public function change($changeValue, $accountId, $form_key)
    {
        $req = new \Praxigento\Accounting\Service\Balance\Request\Change();
        $req->setCustomerAccountId($accountId);
        $req->setChangeValue($changeValue);
        $userId = $this->_authSession->getUser()->getId();
        $req->setAdminUserId($userId);
        $result = $this->_callBalance->change($req);
        return $result;
    }

}