<?php

ini_set('output_buffering', 'off');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Magento\Framework\App\Area;
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
    \Magento\Framework\App\Area::AREA_CRONTAB
); // for remove Area code is not set error

$configLoader = $obj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
$obj->configure($configLoader->load(Area::AREA_CRONTAB));
$emulation = $obj->get('\Magento\Store\Model\App\Emulation');

$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$cron = $obj->get('\Interprise\Logger\Model\Cron\Scheduler');

$obj->get('\Magento\Framework\App\State')->emulateAreaCode(
    \Magento\Framework\App\Area::AREA_CRONTAB,
    [$cron, "execute"]
);
//print_r($storeManager->getStore()->getId()); exit;
//$cron->execute();

//$emulation->stopEnvironmentEmulation();
