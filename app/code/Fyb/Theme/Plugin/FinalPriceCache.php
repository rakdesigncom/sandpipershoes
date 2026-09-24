<?php

namespace Fyb\Theme\Plugin;

use Magento\Catalog\Pricing\Render\FinalPriceBox;

class FinalPriceCache
{
    /**
     * @var \Fyb\Theme\Helper\Product
     */
    protected $productHelper;

    public function __construct(
        \Fyb\Theme\Helper\Product $productHelper
    ) {
        $this->productHelper = $productHelper;
    }

    /**
     * @param FinalPriceBox $subject
     * @param string $cacheKey
     *
     * @return string
     */
    public function afterGetCacheKey(FinalPriceBox $subject, $cacheKey)
    {
        $cacheKey .= $this->productHelper->isSaleCategory();

        return $cacheKey;
    }

    /**
     * @param FinalPriceBox $subject
     * @param array $cacheKeys
     *
     * @return array
     */
    public function afterGetCacheKeyInfo(FinalPriceBox $subject, $cacheKeys)
    {
        $cacheKeys['is_sale_category'] = $this->productHelper->isSaleCategory();

        return $cacheKeys;
    }
}
