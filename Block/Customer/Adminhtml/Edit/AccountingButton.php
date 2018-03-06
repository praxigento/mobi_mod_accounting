<?php

namespace Praxigento\Accounting\Block\Customer\Adminhtml\Edit;

use Praxigento\Accounting\Config as Cfg;

class AccountingButton
    extends \Magento\Customer\Block\Adminhtml\Edit\GenericButton
    implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /** @var \Magento\Framework\AuthorizationInterface */
    private $authorization;
    /** @var \Magento\Customer\Api\AccountManagementInterface */
    private $customerAccountManagement;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
    )
    {
        parent::__construct($context, $registry);
        $this->authorization = $context->getAuthorization();
        $this->customerAccountManagement = $customerAccountManagement;
    }

    public function getButtonData()
    {
        $data = [];
        /* check ACL & store configuration */
        $isAllowed = $this->authorization->isAllowed(Cfg::MODULE . '::' . Cfg::ACL_ACCOUNTS);
        if ($isAllowed) {
            if (true) {
                $customerId = $this->getCustomerId();
                $canModify = $customerId && !$this->customerAccountManagement->isReadonly($this->getCustomerId());
                if ($customerId && $canModify) {
                    $data = [
                        'label' => __('Accounting'),
                        'id' => 'customer-edit-prxgt-accounting',
                        'sort_order' => 100,
                        'on_click' => 'false'
                    ];
                }
            }
        }
        return $data;
    }
}
