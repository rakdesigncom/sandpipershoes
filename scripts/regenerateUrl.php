<?php

ini_set('output_buffering', 'off');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Api\ProductRepositoryInterface;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\Store;
use Magento\Framework\App\State;

$baseDir = dirname(__FILE__, 2);

require $baseDir . '/app/bootstrap.php';

$params = $_SERVER;
$params[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS] = [
    DirectoryList::PUB => [DirectoryList::URL_PATH => ''],
    DirectoryList::MEDIA => [DirectoryList::URL_PATH => 'media'],
    DirectoryList::STATIC_VIEW => [DirectoryList::URL_PATH => 'static'],
    DirectoryList::UPLOAD => [DirectoryList::URL_PATH => 'media/upload'],
];
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);

$obj = $bootstrap->getObjectManager();
$obj->get('\Magento\Framework\App\State')->setAreaCode(
    \Magento\Framework\App\Area::AREA_GLOBAL
); // for remove Area code is not set error
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');

$productStatus = $obj->get(\Magento\Catalog\Model\Product\Attribute\Source\Status::class);
$productVisibility = $obj->get(\Magento\Catalog\Model\Product\Visibility::class);
$productCollection = $obj->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory')->create();
$productCollection->addAttributeToSelect('*')
//    ->addAttributeToFilter('sku', ['in' => $skus])
    ->addAttributeToFilter('type_id', ['eq' => 'configurable']);

$productUrlRewriteGenerator = $obj->get(\Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator::class);
$urlPersist = $obj->get(\Magento\UrlRewrite\Model\UrlPersistInterface::class);
$appendRewrites = $obj->get('\Magento\CatalogUrlRewrite\Model\Products\AppendUrlRewritesToProducts');
$collectionFactory = $obj->get('\Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory');
$productRepository = $obj->get(ProductRepositoryInterface::class);
$productIds = [];

foreach ($productCollection as $product) {

    $storeIds = $product->getStoreIds();
    $countStoreIds = count($storeIds);
    $needGenerate = false;

    if (!$storeIds) {
        continue;
    }

    $collection = $collectionFactory->create();
    $collection->addFieldToFilter('target_path', ['eq' => 'catalog/product/view/id/' . $product->getId()])
        ->addStoreFilter($storeIds, false);

//    if ($collection->count() != $countStoreIds) {
        $urlPersist->deleteByData([
            UrlRewrite::ENTITY_ID => $product->getId(),
            UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $storeIds,
        ]);

        try {
            $appendRewrites->execute([$product], $storeIds);
//            $urlPersist->replace(
//                $productUrlRewriteGenerator->generate($product)
//            );

            $productIds[] = $product->getId();
        } catch (\Exception $e) {
            print_r([
                $storeIds,
                $product->getId(),
                $e->getMessage()
            ]);
        }
//    }
}
