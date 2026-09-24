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
$configurableInstance = $obj->get('\Magento\ConfigurableProduct\Model\Product\Type\Configurable');
$csvReader = $obj->get('\Magento\Framework\File\Csv');
$orderRepository = $obj->get('\Magento\Sales\Model\OrderFactory');
$resource = $obj->get('\Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

$storeId = 2;
$isTest = false;
$prefix = 'b2b';

$orderCollection = $obj->get(\Magento\Sales\Model\ResourceModel\Order\CollectionFactory::class)->create();
$orderCollection->getSelect()->order('entity_id DESC')->limit(1);
$lastOrderId = $orderCollection->getFirstItem()->getData('entity_id') + 500;
$orderIdsMap = [];

$store = $storeManager->getStore($storeId);
$storeFullName = $store->getName();
if ($storeFullName == 'SandPiperShoes Trade Store View') {
    $storeFullName = "SandPiperShoes Trade\nSandPiperShoes Trade Store\n" . $storeFullName;
} else {
    $storeFullName = "SandPiperShoes\nSandPiperShoes Store\n" . $storeFullName;
}

function prepareData($data, $type = '')
{
    global $storeId;
    global $storeFullName;
    global $store;

    $header = $data[0];
    unset($data[0]);

    $newData = [];
    foreach ($data as $row) {
        $newRow = array_combine($header, $row);

        if (isset($newRow['store_id'])) {
            $newRow['store_id'] = $storeId;
        }
        switch ($type) {
            case 'order':
                $newRow['store_name'] = $storeFullName;
                if ($newRow['customer_email'] == 'accounts@millercare.co.uk') {
                    $newRow['customer_email'] = 'web@millercare.co.uk';
                }
                $newRow['store_name'] = $storeFullName;
                break;
            default:
                foreach ($newRow as $key => $value) {
                    if ($value === '') {
                        $newRow[$key] = null;
                    }
                    if (strstr($key, 'email') && $value == 'accounts@millercare.co.uk') {
                        $newRow[$key] = 'web@millercare.co.uk';
                    }
                }
                break;
        }
        $newData[] = $newRow;
    }

    return $newData;
}

$orders = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order.csv');
$orders = prepareData($orders, 'order');
$customerMapping = [];
$orderIncrementMap = [];
foreach ($orders as $order) {
    if (!strstr($order['customer_email'], 'millercare.co.uk')) {
        continue;
    }
    $checkOrder = $orderRepository->create()->load($order['increment_id'], 'increment_id');
    if ($checkOrder->getId()) {
        continue;
    }

    try {
        $customer = $customerRepository->get($order['customer_email']);
    } catch (\Exception $e) {
        $customer = null;
    }

    $customerMapping[$order['customer_id']] = $customer ? $customer->getId() : null;
    $order['customer_id'] = $customer ? $customer->getId() : null;
    $orderIdsMap[$order['entity_id']] = $lastOrderId;
    $order['entity_id'] = $lastOrderId;

    $orderIncrementMap[$order['entity_id']] = $order['increment_id'];
    $lastOrderId++;

    if (!$isTest) {
        $connection->insert('sales_order', $order);
    }
}
print_r(["Orders" => count($orderIdsMap)]);

$addresses = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order_address.csv');
$addresses = prepareData($addresses, 'address');
$addMapping = [];
$lastOrderAddressId = $resource->getConnection()->select()->from('sales_order_address')->order('entity_id DESC')->limit(1)->query()->fetchColumn() + 500;
foreach ($addresses as $address) {
    if (!isset($orderIdsMap[$address['parent_id']])) {
        continue;
    }

    $address['customer_address_id'] = null;
    foreach ($address as $key => $field) {
        $address[$key] = $field || $field === '0' ? $field : null;
    }
    $address['is_dropship'] = 0;

    $address['entity_id'] = $lastOrderAddressId + $address['entity_id'];
    $address['parent_id'] = $orderIdsMap[$address['parent_id']];
    $addMapping[] = $address['entity_id'];
    if (!$isTest) {
        $connection->insert('sales_order_address', $address);
    }
}
print_r(["Addresses" => count($addMapping)]);

$items = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order_item.csv');
$items = prepareData($items, 'item');
$lastOrderItemId = $resource->getConnection()->select()->from('sales_order_item')->order('item_id DESC')->limit(1)->query()->fetchColumn() + 500;
$orderItemsCount = [];
foreach ($items as $item) {
    if (!isset($orderIdsMap[$item['order_id']])) {
        continue;
    }

    $orderItemsCount[] = $item['item_id'];
    $item['order_id'] = $orderIdsMap[$item['order_id']];
    $item['item_id'] = $lastOrderItemId + $item['item_id'];
    if ($item['parent_item_id']) {
        $item['parent_item_id'] = $lastOrderItemId + $item['parent_item_id'];
    }

    if (!$isTest) {
        $connection->insert('sales_order_item', $item);
    }
}
print_r(["orderItemsCount" => count($orderItemsCount)]);

$payments = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order_payment.csv');
$payments = prepareData($payments, 'payment');
$countPayments = [];
foreach ($payments as $payment) {
    if (!isset($orderIdsMap[$payment['parent_id']])) {
        continue;
    }

    $countPayments[] = $payment['entity_id'];
    unset($payment['entity_id']);
    $payment['parent_id'] = $orderIdsMap[$payment['parent_id']];

    if (!$isTest) {
        $connection->insert('sales_order_payment', $payment);
    }
}
print_r(["orderPayments" => count($countPayments)]);

$taxes = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order_tax.csv');
$taxes = prepareData($taxes, 'tax');
$lastOrderTaxId = $resource->getConnection()->select()->from('sales_order_tax')->order('tax_id DESC')->limit(1)->query()->fetchColumn() + 500;
$orderTaxMappings = [];
foreach ($taxes as $tax) {
    if (!isset($orderIdsMap[$tax['order_id']])) {
        continue;
    }
    $tax['order_id'] = $orderIdsMap[$tax['order_id']];
    $orderTaxMappings[$tax['tax_id']] = $lastOrderTaxId + $tax['tax_id'];
    $tax['tax_id'] = $lastOrderTaxId + $tax['tax_id'];

    if (!$isTest) {
        $connection->insert('sales_order_tax', $tax);
    }
}
print_r(["orderTaxMappings" => count($orderTaxMappings)]);

$taxItems = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_order_tax_item.csv');
$taxItems = prepareData($taxItems, 'tax');
$taxItemsCount = [];
foreach ($taxItems as $taxItem) {
    if (!isset($orderTaxMappings[$taxItem['tax_id']])) {
        continue;
    }

    $taxItem['associated_item_id'] = null;
    $taxItem['item_id'] = $taxItem['item_id'] ?: null;
    $taxItemsCount[] = $taxItem['tax_item_id'];
    unset($taxItem['tax_item_id']);
    if ($taxItem['tax_id']) {
        $taxItem['tax_id'] = $taxItem['tax_id'] + $lastOrderTaxId;
    }
    if ($taxItem['item_id']) {
        $taxItem['item_id'] = $taxItem['item_id'] + $lastOrderItemId;
    }


    if (!$isTest) {
        $connection->insert('sales_order_tax_item', $taxItem);
    }
}
print_r(["orderTaxItemsMappings" => count($taxItemsCount)]);

$shipments = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_shipment.csv');
$shipments = prepareData($shipments, 'shipment');
$orderShipmentMappings = [];
$lastOrderShipmentId = $resource->getConnection()->select()->from('sales_shipment')->order('entity_id DESC')->limit(1)->query()->fetchColumn() + 500;
foreach ($shipments as $shipment) {
    if (!isset($orderIdsMap[$shipment['order_id']])) {
        continue;
    }

    $orderShipmentMappings[$shipment['entity_id']] = $lastOrderShipmentId + $shipment['entity_id'];
    $shipment['entity_id'] = $lastOrderShipmentId + $shipment['entity_id'];
    $shipment['order_id'] = $orderIdsMap[$shipment['order_id']];
    $shipment['shipping_address_id'] = $lastOrderAddressId + $shipment['shipping_address_id'];
    $shipment['billing_address_id'] = $lastOrderAddressId + $shipment['billing_address_id'];
    $shipment['customer_id'] = $customerMapping[$shipment['customer_id']] ?? null;

    if (!$isTest) {
        $connection->insert('sales_shipment', $shipment);
    }
}
print_r(["orderShipmentMappings" => count($orderShipmentMappings)]);

$shipmentItems = $csvReader->getData($baseDir . '/scripts/csv/order/' . $prefix . '/sales_shipment_item.csv');
$shipmentItems = prepareData($shipmentItems, 'shipment_item');
$checkItems = [];
foreach ($shipmentItems as $shipmentItem) {
    if (!isset($orderShipmentMappings[$shipmentItem['parent_id']])) {
        continue;
    }
    try {
        $product = $productRepository->get($shipmentItem['sku'], false, $storeId);
    } catch (\Exception $e) {
        $product = null;
    }
    if ($product) {
        $shipmentItem['product_id'] = $product->getId();
    }
    $checkItems[] = $shipmentItem['entity_id'];
    unset($shipmentItem['entity_id']);
    $shipmentItem['parent_id'] = $lastOrderShipmentId + $shipmentItem['parent_id'];
    $shipmentItem['order_item_id'] = $lastOrderItemId + $shipmentItem['order_item_id'];

    if (!$isTest) {
        $connection->insert('sales_shipment_item', $shipmentItem);
    }
}
print_r(["OrderShipmentItem" => count($checkItems)]);

$gridPool = $obj->get(\Magento\Sales\Model\ResourceModel\GridPool::class);
$collection = $obj->get(\Magento\Sales\Model\ResourceModel\Order\CollectionFactory::class)->create();
foreach ($orderIncrementMap as $id => $increment) {
    $connection->delete('sales_order_grid', "increment_id = '" . $increment . "'");
    $gridPool->refreshByOrderId($id);
}
