<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Ui\Component\Listing\Column;

/**
 * Add
 */
class Actions
    extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;


    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->get('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$name]['edit'] = [
                    'label' => __('Edit'),
                    'id' => $item['Id']
                ];
//                $item[$name . '_html'] = "<button class='button' translate='yes'><span>Change</span></button>";
//                $item[$name . '_title'] = __('Change account balance');
//                $item[$name . '_submitlabel'] = __('Send');
//                $item[$name . '_cancellabel'] = __('Reset');
//                $item[$name . '_accountid'] = $item['Id'];
//                $item[$name . '_formaction'] = $this->urlBuilder->getUrl('grid/customer/sendmail');
            }
        }

        return $dataSource;
    }
}