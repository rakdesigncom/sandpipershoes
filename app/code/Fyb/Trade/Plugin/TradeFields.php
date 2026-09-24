<?php

namespace Fyb\Trade\Plugin;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class TradeFields
{
    /**
     * @var \Fyb\Trade\Helper\Data
     */
    protected $helper;

    /**
     * @param \Fyb\Trade\Helper\Data $helper
     */
    public function __construct(
        \Fyb\Trade\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param LayoutProcessor $subject
     * @param array $result
     * @param array $jsLayout
     *
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        if (!$this->helper->isTradeStore()) {
            return $jsLayout;
        }

        $shippingConfiguration = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if (isset($shippingConfiguration)) {
            $dropShipAttribute = 'is_dropship';
            $dataScope = 'shippingAddress.custom_attributes';
            $shippingConfiguration[$dropShipAttribute] = [
                'component' => 'Fyb_Trade/js/form/element/dropship',
                'config' => [
                    'customScope' => $dataScope,
                    'template' => 'ui/form/field',
                    'prefer' => 'checkbox',
                    'description' => __('Is this a drop ship order? (Enter address below)'),
                ],
                'dataScope' => $dataScope . '.' . $dropShipAttribute,
                'label' => '',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'value' => true,
                'options' => [],
                'sortOrder' => 0,
                'valueMap' => [
                    'true' => true,
                    'false' => false
                ]
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['before-form']['children']['is_dropship_text'] = [
                'component' => 'Fyb_Trade/js/form/element/dropship_text',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'id' => 'is_dropship_text'
                ],
                'dataScope' => 'shippingAddress.is_dropship_text',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'sortOrder' => 200,
                'id' => 'is_dropship_text'
            ];
        }



        return $jsLayout;
    }
}
