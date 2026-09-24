<?php

/**
 * Created by PhpStorm.
 * User: Manisha
 * Date: 27/02/2020
 * Time: 5:02 PM
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Setup\Exception;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\DB\Adapter\DuplicateException;

class InventoryItemDescription extends Data
{

    public $_prices;

    public $category;

    public $productvisibility;

    public $objectManager;

    public $directory;

    public $attribute_repository;

    protected $_file;

    protected $inventoryStock;

    protected $inventoryItem;

    protected $prices;

    protected $processor;

    protected $productGallery;

    protected $eavConfig;

    protected $attributeOptionManagementInterface;

    protected $attributeOptionLabelFactory;

    protected $attributeOptionFactory;

    protected $_directoryList;

    protected $_io;

    protected $filesystem;

    protected $productFactory;

    protected $productLinks;

    protected $resource;

    protected $connection;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryobj,
        \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer,
        \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CountryclassmappingFactory $classmapping,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact,
        //\Interprise\Logger\Model\InstallwizardFactory $installwizardFactory,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Interprise\Logger\Helper\InventoryStock $inventorystock,
        \Interprise\Logger\Helper\InventoryItem $inventoryitem,
        \Interprise\Logger\Helper\Prices $_prices,
        \Interprise\Logger\Helper\Category $_category,
        \Magento\Framework\Filesystem\DirectoryList $_directory,
        \Magento\Catalog\Model\Product\Gallery\Processor $gallery,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $productGallery,
        \Magento\Eav\Api\AttributeRepositoryInterface $attribute_repository,
        \Magento\Eav\Model\Config $_eavConfig,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagementInterface,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $attributeOptionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $attributeOptionFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $links
    ) {
        $this->inventoryStock = $inventorystock;
        $this->inventoryItem = $inventoryitem;
        $this->prices = $_prices;
        $this->category = $_category;
        //$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->directory = $_directory;
        $this->_product = $product;
        $this->processor = $gallery;
        $this->productGallery = $productGallery;
        $this->attribute_repository = $attribute_repository;
        $this->eavConfig = $_eavConfig;
        $this->attributeOptionManagementInterface = $attributeOptionManagementInterface;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->_file = $file;
        $this->_directoryList = $directoryList;
        $this->_io = $io;
        $this->filesystem = $filesystem;
        $this->productFactory = $productFactory;
        $this->productLinks = $links;
        //$this->directory = $this->objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $this->resource = $resourceCon;
        $this->connection = $this->resource->getConnection();
        parent::__construct(
            $context,
            $httpContext,
            $product,
            $curl,
            $datetime,
            $categorycollection,
            $productCollectionFactory,
            $categoryobj,
            $pricingcustomer,
            $pricelistsFactory,
            $productFactory,
            $session,
            $classmapping,
            $statementaccountFactory,
            $addressFactory,
            $custompaymentFactory,
            $custompaymentitemFactory,
            $paymentmethodfact,
            $installwizardFactory,
            $shippingstoreinterpriseFactory,
            $curlFactory
        );
    }

    public function inventoryItemDescriptionSingle($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

