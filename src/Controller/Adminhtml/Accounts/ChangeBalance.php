<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Controller\Adminhtml\Accounts;

use Praxigento\Accounting\Config as Cfg;

/**
 * This controller should return JSON as result of the processing.
 */
class ChangeBalance
    extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct(            $context        );
    }
}