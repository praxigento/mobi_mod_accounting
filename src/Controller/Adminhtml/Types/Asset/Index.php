<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Types\Asset;

use Praxigento\Accounting\Config as Cfg;

class Index
    extends \Praxigento\Accounting\Controller\Adminhtml\Base
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS_TYPES_ASSET;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_ACCOUNTS_TYPES_ASSET;
        $breadcrumbLabel = 'Asset Types';
        $breadcrumbTitle = 'Asset Types';
        $pageTitle = 'Asset Types';
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