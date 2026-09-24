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
$resource = $obj->get('\Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

$cron = $obj->create(\Interprise\Logger\Helper\InventoryItem::class);
$cron->inventoryItemDescriptionSingle(['DataId' => 'ITEM-007614']);
exit;
//$gridPool = $obj->get(\Magento\Sales\Model\ResourceModel\GridPool::class);
//$collection = $obj->get(\Magento\Sales\Model\ResourceModel\Order\CollectionFactory::class)->create();
//
////$collection->addFieldToFilter('entity_id', 11371);
////$collection->addFieldToFilter('created_at', ['lt' => "2024-04-08"]);
////print_r($collection->getSelect()->__toString()); exit;
//$totalAffected = 0;
//foreach ($collection as $order) {
//    $shippingAddress = $order->getShippingAddress();
//    $billingAddress = $order->getBillingAddress();
//
//    $orderShippingAddressId = $order->getData('shipping_address_id');
//    $orderBillingAddressId = $order->getData('billing_address_id');
//
//    if ($orderShippingAddressId != $shippingAddress->getId() || $orderBillingAddressId != $billingAddress->getId()) {
//        $affected = 0;
//        $affected = $connection->update(
//            'sales_order',
//            [
//                'shipping_address_id' => $shippingAddress->getId(),
//                'billing_address_id' => $billingAddress->getId(),
//            ],
//            'entity_id=' . $order->getId()
//        );
//        $gridPool->refreshByOrderId($order->getId());
//        print_r([$affected, $order->getId()]);
//
//        $totalAffected++;
//    }
//}
//
//print_r([$totalAffected]);
