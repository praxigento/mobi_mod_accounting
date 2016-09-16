<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Types\Oper;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_TYPES_OPER;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_TYPES_OPER;
        $breadcrumbLabel = 'Operation Types';
        $breadcrumbTitle = 'Operation Types';
        $pageTitle = 'Operation Types';
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