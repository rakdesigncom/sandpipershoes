<?php

namespace Interprise\Logger\Model\Erp;

use Magento\Framework\App\ObjectManager;

class Inventory
{
    const DEFAULT_STOCK_ID = 1;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected $sourceItemsSaveInterface;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    protected $sourceItemFactory;

    /**
     * @var \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface
     */
    protected $getStockItemConfiguration;

    /**
     * @var \Magento\InventoryConfigurationApi\Api\SaveStockItemConfigurationInterface
     */
    protected $saveStockItemConfiguration;

    /**
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory
     * @param \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param \Magento\InventoryConfigurationApi\Api\SaveStockItemConfigurationInterface $saveStockItemConfiguration
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration,
        \Magento\InventoryConfigurationApi\Api\SaveStockItemConfigurationInterface $saveStockItemConfiguration
    ) {
        $this->productRepository = $productRepository;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->saveStockItemConfiguration = $saveStockItemConfiguration;
    }

    public function updateProductStock($productId, $stockQty)
    {
        $stockQty = max($stockQty, 0);
        $product = $this->productRepository->getById($productId, false, 0, false);
        if ($product->getTypeId() != 'simple') {
            return;
        }

        /** @var \Magento\InventoryApi\Api\Data\SourceItemInterface $sourceItem */
        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode('default');
        $sourceItem->setSku($product->getSku());
        $sourceItem->setQuantity($stockQty);
        $sourceItem->setStatus(true);
        $this->sourceItemsSaveInterface->execute([$sourceItem]);

        // Update stock item (Advanced inventory msi)
        $stockItemConfiguration = $this->getStockItemConfiguration->execute(
            $product->getSku(), self::DEFAULT_STOCK_ID
        );
        $stockItemConfiguration->setManageStock(true);
        $stockItemConfiguration->setUseConfigBackorders(true);
        $stockItemConfiguration->setUseConfigManageStock(true);
        $stockItemConfiguration->setUseConfigQtyIncrements(true);
        $stockItemConfiguration->setUseConfigQtyIncrements(true);
        $stockItemConfiguration->setUseConfigNotifyStockQty(true);
        $stockItemConfiguration->setUseConfigMinQty(true);
        $stockItemConfiguration->setUseConfigMinSaleQty(true);
        $stockItemConfiguration->setUseConfigMaxSaleQty(true);

        /** @var  $stockItemConfigurationExtension */
        $stockItemConfigurationExtension = $stockItemConfiguration->getExtensionAttributes();
        $stockItemConfigurationExtension->setIsInStock(1);

        // set other stock item properties here ('cataloginventory_stock_item' table in DB)
        $stockItemConfiguration->setExtensionAttributes($stockItemConfigurationExtension);
        $this->saveStockItemConfiguration->execute(
            $product->getSku(),
            self::DEFAULT_STOCK_ID,
            $stockItemConfiguration
        );
    }
}
