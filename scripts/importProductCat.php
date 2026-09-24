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
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$categoryLinkManagement = $obj->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
$csvReader = $obj->get('\Magento\Framework\File\Csv');
$categoryCollection = $obj->create(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class);
$productRepository = $obj->get('Magento\Catalog\Model\ProductRepository');

$productsData = $csvReader->getData($baseDir . '/scripts/csv/Categories.csv');
unset($productsData[0]);

$existingCategories = [];

function getCategoryPath($category, $categoryFactory) {
    $pathIds = explode('/', $category->getPath());
    $pathNames = [];

    foreach ($pathIds as $categoryId) {
        if ($categoryId == 1) {
            continue;
        }
        $categoryModel = $categoryFactory->create()->load($categoryId);
        if ($categoryModel->getId() && $categoryModel->getName()) {
            $pathNames[] = $categoryModel->getName();
        }
    }

    return implode('/', $pathNames);
}

function getCategoriesWithPaths($categoryFactory) {
    $categoryCollection = $categoryFactory->create()->getCollection()
        ->addAttributeToSelect(['name', 'path'])
        ->addIsActiveFilter();

    $categoriesWithPath = [];

    foreach ($categoryCollection as $category) {
        if ($category->getId() == 2) {
            continue;
        }
        $path = getCategoryPath($category, $categoryFactory);
        if (strstr($path, 'B2B')) {
            continue;
        }

        $categoriesWithPath[$path] = $category->getId();
    }

    return $categoriesWithPath;
}
$categoryFactory = $obj->get(\Magento\Catalog\Model\CategoryFactory::class);
$categoriesWithPath = getCategoriesWithPaths($categoryFactory);

function getCategory($catName)
{
    global $categoriesWithPath;
    global $obj;



    if (strpos($catName, '/') === false) {
        return false;
    }
    if (strstr($catName, 'Extra Wide Hosiery')) {
        return false;
    }
    if (count(explode('/', $catName)) > 3) {
        return false;
    }

    $catName = str_replace('/Special Offers', '/Sale', $catName);
    $catName = str_replace('/Ladies Shoes', '/Shoes', $catName);
    $catName = str_replace('/Ladies Sandals', '/Sandals', $catName);
    $catName = str_replace('/Ladies Boots', '/Boots', $catName);
    $catName = str_replace('/Ladies Extra Wide', '/Extra Wide', $catName);
    $catName = str_replace('/Ladies Slippers', '/Slippers', $catName);
    $catName = str_replace('/Ladies Ultra Wide', '/Ultra Wide', $catName);
    $catName = str_replace('/New!', '/New Arrivals', $catName);
    $catName = str_replace('/Boot Offers', '/Boots Offers', $catName);

    $catName = str_replace('/Men\'s Sandals', '/Sandals', $catName);
    $catName = str_replace('/Men\'s Boots', '/Boots', $catName);
    $catName = str_replace('/Men\'s Slippers', '/Slippers', $catName);
    $catName = str_replace('/Men\'s Extra Wide', '/Extra Wide', $catName);
    $catName = str_replace('/Men\'s Ultra Wide', '/Ultra Wide', $catName);
    $catName = str_replace('/Men\'s Shoes', '/Shoes', $catName);
    $catName = str_replace('/Mens Offers', '/Gentlemens Offers', $catName);
    $catName = str_replace("/Men's", '/Gentlemen', $catName);
    $catName = str_replace("SandPiperShoes/Accessories/Extra Wide Socks", 'SandPiperShoes/Socks', $catName);

    if (!isset($categoriesWithPath[$catName])) {
        print_r(['NOTFOUND' => $catName]);
        exit;
    }

    return $categoriesWithPath[$catName];
}


foreach ($productsData as $productData) {
    try {
        $product = $productRepository->get($productData[0]);
    } catch (\Exception) {
        continue;
    }

    $categories = array_filter(
        array_map('trim', explode(',', $productData[4]))
    );

    $categoryIds = [];

    foreach ($categories as $category) {
        $catId = (int)getCategory($category);
        if (!$catId) {
            continue;
        }

        $categoryIds[$catId] = $catId;
    }

    if ($categoryIds) {
        $categoryLinkManagement->assignProductToCategories(
            $product->getSku(),
            $categoryIds
        );
    }
}

