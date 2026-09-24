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
    \Magento\Framework\App\Area::AREA_FRONTEND
); // for remove Area code is not set error
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$registry = $obj->get('\Magento\Framework\Registry');
$cron = $obj->get('\Interprise\Logger\Helper\Data');
$products = $obj->create(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class)->create();
$productfactory = $obj->create(\Magento\Catalog\Model\ProductFactory::class);
$products->addFieldToSelect('*');
$products->addFieldToFilter('type_id', 'simple');

foreach ($products as $productBase) {
    try {
        $product = $productfactory->create()->setStoreId(2)->load($productBase->getId());
        if ($product->getSpecialPrice()) {
            $product->setSpecialPrice('')->setSpecialFromDate('')->setSpecialToDate('');
            $product->save();
        }
    } catch (\Exception $e) {

    }
//    $cron->updateWhosalePrice($product);
//    print_r([$product->getId()]); exit;
}

//$customers = $obj->create(\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory::class)->create();
//$customers->addFieldToSelect('*');
//$customers->addFieldToFilter('website_id', 2);
//
//foreach ($customers as $customer) {
//    if ($customer->getData('interprise_defaultprice') == 'Retail') {
//        $customer->setGroupId(3);
//    }
//    if ($customer->getData('interprise_defaultprice') == 'Wholesale') {
//        $customer->setGroupId(2);
//    }
//
//    if (!$customer->getData('interprise_defaultprice')) {
//        continue;
//    }
//    $customer->save();
//}
