<?php

namespace Fyb\Theme\Plugin;

use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;

class CartConfigurableProductName
{
    /**
     * @param AbstractItem $subject
     * @param $result
     * @param Item $item
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item)
    {
        if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $result['product_name'] = $item->getName();
        }

        return $result;
    }
}
