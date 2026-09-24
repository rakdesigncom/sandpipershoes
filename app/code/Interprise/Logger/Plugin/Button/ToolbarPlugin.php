<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Interprise\Logger\Plugin\Button;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\ToolbarInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\LoginAsCustomerAdminUi\Ui\Customer\Component\Button\DataProvider;
use Magento\LoginAsCustomerApi\Api\ConfigInterface;

/**
 * Plugin for \Magento\Backend\Block\Widget\Button\Toolbar.
 */
class ToolbarPlugin
{
    /**
     * @var Escaper
     */
    private $escaper;

    private $urlBuilder;

    /**
     * ToolbarPlugin constructor.
     * @param Escaper $escaper
     */
    public function __construct(
        Escaper $escaper,
        UrlInterface $urlBuilder,
    ) {
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Add Login as Customer button.
     *
     * @param ToolbarInterface $subject
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePushButtons(
        ToolbarInterface $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ): void {
        $nameInLayout = $context->getNameInLayout();

        $order = $this->getOrder($nameInLayout, $context);
        if ($order) {
            $buttonList->add(
                'sync_to_interprise',
                [
                    'label' => __('Sync to Interprise'),
                    'on_click' => 'window.interpriseSyncConfirmationPopup("'
                        . $this->escaper->escapeHtml($this->escaper->escapeJs(
                            $this->urlBuilder->getUrl('interprise_logger/changelog/queueadd', ['order_id' => $order['entity_id']])
                        ))
                        . '")',
                ],
                -1
            );
        }
    }

    /**
     * Extract order data from context.
     *
     * @param string $nameInLayout
     * @param AbstractBlock $context
     * @return array|null
     */
    private function getOrder(string $nameInLayout, AbstractBlock $context)
    {
        switch ($nameInLayout) {
            case 'sales_order_edit':
                return $context->getOrder();
            case 'sales_invoice_view':
                return $context->getInvoice()->getOrder();
            case 'sales_shipment_view':
                return $context->getShipment()->getOrder();
            case 'sales_creditmemo_view':
                return $context->getCreditmemo()->getOrder();
        }

        return null;
    }
}
