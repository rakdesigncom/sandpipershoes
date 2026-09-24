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
$customerRepository = $obj->get('\Magento\Customer\Api\CustomerRepositoryInterface');
$productRepository = $obj->get('\Magento\Catalog\Api\ProductRepositoryInterface');
$productFactory = $obj->get('\Magento\Catalog\Model\ProductFactory');
$configurableInstance = $obj->get('\Magento\ConfigurableProduct\Model\Product\Type\Configurable');
$csvReader = $obj->get('\Magento\Framework\File\Csv');
$resource = $obj->get('\Magento\Framework\App\ResourceConnection');
$configurableType  = $obj->get('\Magento\ConfigurableProduct\Model\Product\Type\Configurable');
$connection = $resource->getConnection();

$products = $obj->create(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class)->create();
$products->addFieldToFilter('type_id', 'simple')
    ->addFieldToFilter('sku', ['like' => 'SOFIA%']);

foreach ($products as $productInitial) {
    $product = $productFactory->create()->setStoreId(1)->load($productInitial->getId());
    $parentIds = $configurableType->getParentIdsByChild($product->getId());
    $parentId = array_shift($parentIds);

    if (!$parentId) {
        continue;
    }

    $images = $product->getMediaGallery()['images'];
    $baseImage = '';
    foreach ($images as $image) {
        $baseImage = $image['file'] ?? '';
    }

    if ($baseImage && $baseImage != 'no_selection') {
        $product->setImage($baseImage);
        $product->setSmallImage($baseImage);
        $product->setThumbnail($baseImage);
        $product->setSwatchImage($baseImage);
        $product->save();
        $connection->delete('catalog_product_entity_varchar', "entity_id = " . $product->getId() .
            " AND attribute_id IN (87, 88, 109, 110, 135, 89, 111) AND store_id != 0");
    }
}
