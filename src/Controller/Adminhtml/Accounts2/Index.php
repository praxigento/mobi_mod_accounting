<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Controller\Adminhtml\Accounts2;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_ACCOUNTS2;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_ACCOUNTS2;
        $breadcrumbLabel = 'Accounts2';
        $breadcrumbTitle = 'Accounts2';
        $pageTitle = 'Accounts2';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }

    protected function _isAllowed()
    {
        $result = parent::_isAllowed();
        $result = $result && $this->_authorization->isAllowed(Cfg::ACL_ACCOUNTS_ACCOUNTS2);
        return $result;
    }
}