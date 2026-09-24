<?php

namespace Fyb\Theme\Observer;

use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class ProductBackorderStore implements ObserverInterface
{
    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $stockState;

    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockState = $stockState;
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * Observer for catalog_product_is_salable_after
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $salableProduct = $observer->getSalable();
        $product = $salableProduct->getProduct();

        if ($product->getTypeId() == 'simple' &&
            $this->stockConfiguration->getBackorders($product->getStoreId()) == Stock::BACKORDERS_NO
        ) {
            $salableProduct->setIsSalable($this->stockState->verifyStock($product->getId()));
        }
    }
}

