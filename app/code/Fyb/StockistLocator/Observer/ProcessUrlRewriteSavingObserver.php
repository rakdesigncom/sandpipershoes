<?php

namespace Fyb\StockistLocator\Observer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogUrlRewrite\Model\GetVisibleForStores;
use Magento\CatalogUrlRewrite\Model\Map\UrlRewriteFinder;
use Magento\CatalogUrlRewrite\Model\Products\AppendUrlRewritesToProducts;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreResolver\GetStoresListByWebsiteIds;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;


    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlRewriteFinder
     */
    private $urlRewriteFinder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    private $urlRewriteFactory;
    /**
     * @param UrlPersistInterface $urlPersist
     * @param AppendUrlRewritesToProducts $appendRewrites
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlRewriteFinder $urlRewriteFinder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        ScopeConfigInterface $scopeConfig,
        UrlRewriteFinder $urlRewriteFinder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory
    ) {
        $this->urlPersist = $urlPersist;
        $this->scopeConfig = $scopeConfig;
        $this->urlRewriteFinder = $urlRewriteFinder;
        $this->storeManager = $storeManager;
        $this->urlRewriteFactory = $urlRewriteFactory;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param Observer $observer
     *
     * @return void
     * @throws UrlAlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $stockist = $observer->getEvent()->getObject();

        $this->regenerateStockistUrlRewrites($stockist);
    }

    public function regenerateStockistUrlRewrites($stockist)
    {
        if ($this->isNeedUpdateRewrites($stockist)) {
            $this->deleteObsoleteRewrites($stockist);
            $this->addMissingRewrites($stockist);
        }
    }

    /**
     * Is product rewrites need to be updated
     *
     * @param Product $product
     *
     * @return bool
     */
    private function isNeedUpdateRewrites($stockist): bool
    {
        return $stockist->dataHasChangedFor('url_key')
            || $this->isMissingUrlRewrites($stockist);
    }

    /**
     * Check if url rewrites are missing for a store
     *
     * @param $stockist
     *
     * @return bool
     */
    private function isMissingUrlRewrites($stockist): bool
    {
        $stores = $this->storeManager->getStores();
        $storesCount = count($stores);
        foreach ($stores as $store) {
            $urlRewrite = $this->urlRewriteFinder->findAllByData(
                $stockist->getId(),
                $store->getId(),
                'stockist'
            );

            if (count($urlRewrite) != 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove obsolete Url rewrites
     *
     */
    private function deleteObsoleteRewrites($stockist): void
    {
        //do not perform redundant delete request for new
        if ($stockist->getOrigData('entity_id') === null) {
            return;
        }

        $allStores = $this->storeManager->getStores();
        $storesToRemove = [];
        foreach ($allStores as $store) {
            $storesToRemove[] = $store->getId();
        }

        $storesToRemove = array_filter(array_unique($storesToRemove));

        if ($storesToRemove) {
            $this->urlPersist->deleteByData(
                [
                    UrlRewrite::ENTITY_ID => $stockist->getId(),
                    UrlRewrite::ENTITY_TYPE => 'stockist',
                    UrlRewrite::STORE_ID => $storesToRemove,
                ]
            );
        }
    }

    /**
     * Add missing url rewrites
     *
     * @param Product $product
     *
     * @return void
     * @throws UrlAlreadyExistsException
     */
    private function addMissingRewrites($stockist)
    {
        $storesToAdd = $this->storeManager->getStores();

        $urls = [];
        foreach ($storesToAdd as $store) {
            $urls[] = $this->urlRewriteFactory->create()
                ->setEntityType('stockist')
                ->setEntityId($stockist->getId())
                ->setRequestPath($stockist->getUrlKey())
                ->setTargetPath('stockist/view/index/id/' . $stockist->getId())
                ->setStoreId($store->getId());

            $urls[] = $this->urlRewriteFactory->create()
                ->setEntityType('stockist')
                ->setEntityId($stockist->getId())
                ->setRequestPath($stockist->getUrlKey() . '.html')
                ->setTargetPath($stockist->getUrlKey())
                ->setRedirectType(OptionProvider::PERMANENT)
                ->setIsAutogenerated(0)
                ->setStoreId($store->getId());
        }

        $this->urlPersist->replace($urls);
    }
}
