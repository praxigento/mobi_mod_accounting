<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Operations;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_OPERATIONS;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_OPERATIONS;
        $breadcrumbLabel = 'Operations';
        $breadcrumbTitle = 'Operations';
        $pageTitle = 'Operations';
        parent::__construct(
            $context,
            $resultPageFactory,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
    }
}