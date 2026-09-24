<?php

ini_set('output_buffering', 'off');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;

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

$categoryFactory = $obj->get(\Magento\Catalog\Model\CategoryFactory::class);
$categoryRepository = $obj->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
$storeManager = $obj->get(\Magento\Store\Model\StoreManagerInterface::class);
$scopeConfig = $obj->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);

// Define source and destination store IDs
$sourceStoreId = 1; // Change to the source store ID
$destinationStoreId = 2; // Change to the destination store ID

echo "Fetching categories from source store ID: $sourceStoreId\n";

// Get root category ID of source and destination stores
$sourceRootCategoryId = $storeManager->getStore($sourceStoreId)->getRootCategoryId();
$destinationRootCategoryId = $storeManager->getStore($destinationStoreId)->getRootCategoryId();

$objectManager = $obj;
$categoryCollectionFactory = $objectManager->get(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class);
$urlRewriteCollectionFactory = $objectManager->get(\Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory::class);
$urlPersist = $objectManager->get(\Magento\UrlRewrite\Model\UrlPersistInterface::class);
$urlRewriteGenerator = $objectManager->get(\Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator::class);
$categoryRepository = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
$categoryPathGenerator = $objectManager->get(\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::class);


$categoryCollection = $categoryCollectionFactory->create()
    ->addAttributeToSelect('*')
    ->addFieldToFilter('entity_id', ['nin' => [1, 2, 86]]);

print_r([$categoryCollection->count()]);
foreach ($categoryCollection as $cat) {
    if ($cat->getId() != 213) {
        continue;
    }
    try {
        $storeId = 1;
        $rootCat = 2;
        if (preg_match('/^1\/86\//', $cat->getPath())) {
            $storeId = 2;
            $rootCat = 86;
        }

        $category = $categoryRepository->get($cat->getId(), $storeId);

        if (!$category->getUrlKey()) {
            $categoryGen = $categoryRepository->get($cat->getId(), 0);
            $categoryGen->setUrlKey(null)->setUrlPath(null);
            $categoryGen->setUrlKey($categoryPathGenerator->getUrlKey($categoryGen))
                ->setUrlPath($categoryPathGenerator->getUrlPath($categoryGen));

            print_r([$categoryPathGenerator->getUrlKey($categoryGen), $categoryPathGenerator->getUrlPath($categoryGen)]);

//            $categoryRepository->save($categoryGen);
            $categoryGen->getResource()->saveAttribute($category, 'url_key');
            $categoryGen->getResource()->saveAttribute($category, 'url_path');
//            $categoryGen->save();
            die("DSA");
        }

        die("BBB");
        $categoryId = $category->getId();

        echo "Processing Category ID: {$categoryId}\n";

        // Delete old URL rewrites
        $urlRewriteCollection = $urlRewriteCollectionFactory->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('entity_id', $categoryId);

        foreach ($urlRewriteCollection as $urlRewrite) {
            $urlRewrite->delete();
        }

        // Regenerate URLs
        $category = $categoryRepository->get($categoryId, 0);
        $newUrls = $urlRewriteGenerator->generate($category, true);

        foreach ($newUrls as $key => $urlr) {
            $urlr->setStoreId($storeId);
        }

        print_r($newUrls); exit;
        $urlPersist->replace($newUrls);

        echo "Regenerated URLs for Category ID: {$categoryId}\n";
    } catch (\Exception $e) {
        echo "Error processing category ID: {$categoryId} - " . $e->getMessage() . "\n";
    }
}

echo "Category URL regeneration completed.\n";
