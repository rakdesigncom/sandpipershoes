<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fyb\Theme\Controller\Inventory;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\InventoryCatalogFrontendUi\Model\GetProductQtyLeft;
use Magento\Framework\Controller\ResultInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Get product qty left.
 */
class GetQty extends Action implements HttpGetActionInterface
{
    /**
     * @var ResultFactory
     */
    private $resultPageFactory;

    /**
     * @var ProductQty
     */
    private $productQty;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @param Context $context
     * @param ResultFactory $resultPageFactory
     * @param GetProductQtyLeft $productQty
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        Context $context,
        ResultFactory $resultPageFactory,
        GetProductQtyLeft $productQty,
        StockResolverInterface $stockResolver,
        GetProductSalableQtyInterface $getProductSalableQty
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->productQty = $productQty;
        $this->stockResolver = $stockResolver;
        $this->getProductSalableQty = $getProductSalableQty;
    }

    /**
     * Get qty left for product.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $skus = $this->getRequest()->getParam('skus');
        $salesChannel = $this->getRequest()->getParam('channel');
        $salesChannelCode = $this->getRequest()->getParam('salesChannelCode');
        $resultJson = $this->resultPageFactory->create(ResultFactory::TYPE_JSON);
        $result = [];

        if ($skus && $salesChannel != null && $salesChannelCode != null) {
            try {
                $stockId = $this->stockResolver->execute($salesChannel, $salesChannelCode)->getStockId();
                foreach ($skus as $sku) {
                    $result[$sku] = $this->getProductSalableQty->execute($sku, (int)$stockId);
                }
            } catch (LocalizedException $e) {
            }
        }
        $resultJson->setData($result);

        return $resultJson;
    }
}
