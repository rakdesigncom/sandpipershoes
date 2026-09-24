<?php

namespace Interprise\Logger\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;

class FailedOrderActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    const URL_PATH_DELETE = 'interprise_logger/failedorders/delete';

    const URL_PATH_DETAILS = 'interprise_logger/failedorders/details';

    const URL_PATH_EDIT = 'sales/order/view';

    protected $urlBuilder;

    protected $escaper;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['Increment_id'])) {
                    $html = '<a href="' . $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['order_id' => $item['Increment_id']]) . '" target="_blank">' . $item['Increment_id'] . '</a>';
                    $item[$name] = $this->escaper->escapeHtml($html, ['a']);
                }
            }
        }

        return $dataSource;
    }
}
