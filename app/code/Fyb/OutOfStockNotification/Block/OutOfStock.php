<?php

namespace Fyb\OutOfStockNotification\Block;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class OutOfStock extends \Magento\Catalog\Block\Product\View
{
    public function getOutOfStockProducts()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() != 'configurable') {
            return [];
        }

        $usedProducts = $product->getTypeInstance()->getUsedProducts($product);
        $options = $this->getOptions();
        $outOfStock = [];

        foreach ($usedProducts as $usedProduct) {
            if (isset($options[$usedProduct->getSku()]) && !$usedProduct->isSalable()) {
                $options[$usedProduct->getSku()]['product_id'] = $usedProduct->getId();
                $outOfStock[] = $options[$usedProduct->getSku()];
            }
        }

        return $outOfStock;
    }

    public function getProductOptions()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() != 'configurable') {
            return [];
        }

        return $product->getTypeInstance()->getUsedProducts($product);
    }

    public function getOptions()
    {
        $product = $this->getProduct();

        if ($product->getTypeId() != 'configurable') {
            return [];
        }

        $options = [];
        $configurableOptions = $product->getTypeInstance()->getConfigurableOptions($product);
        $countOptions = count($configurableOptions);
        foreach ($product->getTypeInstance()->getConfigurableOptions($product) as $attr) {
            foreach ($attr as $p) {
                if (!$p['option_title']) {
                    continue;
                }
                $options[$p['sku']][$p['attribute_code']] = $p['option_title'];
            }
        }

        foreach ($options as $sku => $optionsData) {
            if (count($optionsData) != $countOptions) {
                unset($options[$sku]);
            }
        }

        return $options;
    }
}

