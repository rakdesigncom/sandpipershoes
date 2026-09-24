<?php

namespace Fyb\Theme\Helper;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Product extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->registry = $registry;
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function isSaleCategory()
    {
        $category = $this->getCurrentCategory();

        return $category && $category->getId() && $category->getData('is_category_sale');
    }

    public function getConfigurableSaleOption($product)
    {
        $usedProducts = $product->getTypeInstance(true)->getUsedProducts($product);

        $saleOptions = [];

        /** @var \Magento\Catalog\Model\Product $usedProduct */
        foreach ($usedProducts as $usedProduct) {
            if (!$usedProduct->isAvailable()) {
                continue;
            }

            $regularPriceUsed = $usedProduct->getPriceInfo()->getPrice('regular_price')->getValue();
            $finalPriceUsed = $usedProduct->getPriceInfo()->getPrice('final_price')->getValue();

            if ($finalPriceUsed < $regularPriceUsed) {
                $saleOptions[] = [
                    'product' => $usedProduct,
                    'color' => $usedProduct->getAttributeText('color'),
                ];
            }
        }

        if ($saleOptions) {
            usort($saleOptions, function($a, $b) {
                return $a['color'] <=> $b['color'];
            });
            return $saleOptions[0]['product'];
        }

        return null;
    }
}
