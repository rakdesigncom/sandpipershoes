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

$categoryModel = $obj->create(\Magento\Catalog\Model\ResourceModel\Category\Collection::class);
$categories = $categoryModel->addAttributeToSelect('*')
    ->addAttributeToFilter('path', ['like' => "1/$sourceRootCategoryId/%"])
    ->setStoreId($sourceStoreId);

$categoryMap = []; // Mapping source category ID to new category ID

foreach ($categories as $category) {
//    if ($category->getLevel() != 2) {
//        continue;
//    }

    try {
        /** @var \Magento\Catalog\Model\Category $newCategory */
        $newCategory = $categoryFactory->create();
        $newCategory->setName($category->getName());
        $newCategory->setIsActive($category->getIsActive());
        $newCategory->setIncludeInMenu($category->getIncludeInMenu());
        $newCategory->setIsAnchor($category->getIsAnchor());
        $newCategory->setStoreId($destinationStoreId);

        $customAttributes = [];
        foreach ($category->getCustomAttributes() as $customAttribute) {
            $customAttributes[$customAttribute->getAttributeCode()] = $customAttribute->getValue();
        }
        $newCategory->addData($customAttributes);

        // Handle Parent Category Mapping
        $parentId = $category->getParentId();
        if ($parentId == $sourceRootCategoryId) {
            $newParentId = $destinationRootCategoryId;
        } else {
            $newParentId = $categoryMap[$parentId] ?? $destinationRootCategoryId;
        }

        $newCategory->setParentId($newParentId);
        $newCategory->setPath(str_replace('/2/', '/86/', $category->getPath()));
        $newCategory->setUrlKey(null)->setUrlPath(null);

        // Save the new category
        $savedCategory = $categoryRepository->save($newCategory);

        $categoryMap[$category->getId()] = $savedCategory->getId();

        echo "Category '{$category->getName()}' copied successfully!\n";
    } catch (\Exception $e) {
        echo "Error copying category '{$category->getName()}': " . $e->getMessage() . "\n";
        die("DSA");
    }
}

echo "Category copy process completed!\n";
