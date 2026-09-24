<?php

namespace Fyb\Theme\Plugin;

use Magento\InventoryCatalog\Model\ResourceModel\SetDataToLegacyStockStatus;

class HotFixStockStatus
{
    /**
     * @param SetDataToLegacyStockStatus $subject
     * @param string $sku
     * @param float $quantity
     * @param int $status
     *
     * @return array
     */
    public function beforeExecute(SetDataToLegacyStockStatus $subject, string $sku, float $quantity, int $status): array
    {
        return [$sku, $quantity, 1];
    }
}
