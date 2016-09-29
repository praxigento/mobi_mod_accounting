<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Accounts;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_ACCOUNTS;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_ACCOUNTS;
        $breadcrumbLabel = 'Accounts';
        $breadcrumbTitle = 'Accounts';
        $pageTitle = 'Accounts';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}