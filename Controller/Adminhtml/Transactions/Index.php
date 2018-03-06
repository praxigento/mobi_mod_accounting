<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Transactions;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_TRANSACTIONS;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_TRANSACTIONS;
        $breadcrumbLabel = 'Transactions';
        $breadcrumbTitle = 'Transactions';
        $pageTitle = 'Transactions';
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