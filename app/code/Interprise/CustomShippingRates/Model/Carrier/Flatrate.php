<?php

namespace Interprise\CustomShippingRates\Model\Carrier;



use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;

use Magento\Quote\Model\Quote\Address\RateRequest;

use Magento\Shipping\Model\Carrier\AbstractCarrier;

use Magento\Shipping\Model\Carrier\CarrierInterface;

use Magento\Shipping\Model\Rate\Result;



/**

 * Flat rate shipping model

 *

 * @api

 * @since 100.0.2

 */

class Flatrate extends AbstractCarrier implements CarrierInterface

{

    /**

     * @var string

     */

    protected $_code = 'flatrate';

    protected $quoteRepository;

    protected $_coreSession;

    /**

     * @var bool

     */

    protected $_isFixed = true;



    /**

     * @var \Magento\Shipping\Model\Rate\ResultFactory

     */

    protected $_rateResultFactory;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;



    /**

     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory

     */

    protected $_rateMethodFactory;
    


    /**

     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface

     */

    private $cookieManager;

 

    /**

     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory

     */

    private $cookieMetadataFactory;



    /**

     * @var ItemPriceCalculator

     */

    private $itemPriceCalculator;

    protected $customerSession;

    private $_customController;

    /**

     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory

     * @param \Psr\Log\LoggerInterface $logger

     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory

     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory

     * @param ItemPriceCalculator $itemPriceCalculator

     * @param array $data

     */

    public function __construct(

        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,

        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Interprise\CustomShippingRates\Controller\Index\Index $customController,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,

        \Magento\Customer\Model\Session $customerSession,

        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,

        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,

        \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,

        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,

        \Magento\Framework\Session\SessionManagerInterface $coreSession,

        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        array $data = []

    ) {

        $this->_rateResultFactory = $rateResultFactory;

        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_customController = $customController;
        $this->_customerSession = $customerSession;

        $this->quoteRepository = $quoteRepository;

        $this->_coreSession = $coreSession;

        $this->itemPriceCalculator = $itemPriceCalculator;
        $this->_logger = $logger;
        $this->cookieManager = $cookieManager;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->cookieMetadataFactory = $cookieMetadataFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

    }



    /**

     * Collect and get rates

     *

     * @param RateRequest $request

     * @return Result|bool

     * @SuppressWarnings(PHPMD.CyclomaticComplexity)

     * @SuppressWarnings(PHPMD.NPathComplexity)

     */

    public function collectRates(RateRequest $request)

    {

        if (!$this->getConfigFlag('active')) {

            return false;

        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // echo '<pre>';

        // var_dump($request);
        // die;


        $freeBoxes = $this->getFreeBoxesCount($request);

        $this->setFreeBoxes($freeBoxes);



        /** @var Result $result */

        $result = $this->_rateResultFactory->create();



        // $shippingPrice = $this->getShippingPrice($request, $freeBoxes);



        //++++++++++++ custom code - Nitin edit



        $shippingPrice = 0.00;

        #---->getting customer related date
        
        $customerId=$this->_customerSession->getCustomer()->getId();

        $customerGroup=$this->_customerSession->getCustomer()->getGroupId();

        $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');

        $addressFactory = $objectManager->get('Magento\Customer\Api\AddressRepositoryInterface');

        $customerData = $customerRepository->getById($customerId);

        $pricetype=$customerData->getCustomAttribute('interprise_defaultprice')->getValue();

        $shippingAddressId = $customerData->getDefaultShipping();
        $contmethod = $this->_customController->test();
        $checkoutsession = $objectManager->get('Magento\Checkout\Model\Session');
        $this->_coreSession->start();
        $addressId =  $this->_coreSession->getCustomerAddressCurrentId();
        $addressId=explode('-',$addressId);

        $addressId=$addressId[0];
        
        
        if($addressId == 0 || $addressId == null || empty($addressId)){

            $shppingvalue = '*';

        }else{

            // $this->_coreSession->unsCustomerAddressCurrentId();
            $shippingAddress = $addressFactory->getById($addressId);

            $shppingvalue = $shippingAddress->getCustomAttribute('interprise_shippingmethod')->getValue();

            if(strtolower($shppingvalue)!='rm-free' || $shppingvalue=='' || $shppingvalue==null){

                $shppingvalue = '*';

            }

        }
        
        

        // echo $addressId;
        

        #----->Getting cart item data

        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        $items = $cart->getQuote()->getAllItems();

        $weight = 0;
        $helper = $objectManager->create('\Magento\Checkout\Helper\Cart');
        $con=$helper->getSummaryCount();
        $pairs = $cart->getQuote()->getItemsQty();
        $currentquoteid = $checkoutsession->getQuote()->getId();
        $this->_logger->info("quote id-".$currentquoteid);
        $this->_logger->info($con.'total pairs'.$pairs);
        $this->_logger->info('items pairs'.count($items));
        // 'adf-'.$addressId = $checkoutsession->getQuote()->getShippingAddress()->getId();

        $quote = $objectManager->create('Magento\Quote\Model\Quote')->load($currentquoteid);

        $reduce=false;
        $qty=0;

        $this->_logger->info('qqq-'.$pairs);
        // die;

        

        if($weight<=2){

            $tbweight=2;

        }else{

            $tbweight=999999;

        }
        $this->_logger->info( $customerId.'<br>'.$shppingvalue.'<br>'.$pairs.'<br>'.$pricetype.'<br>'.$addressId);
        
        $rule = $this->getShippingRules($request);

        #-----> fetch data from custom table

        $model=$objectManager->create('Interprise\CustomShippingRates\Model\CustomShipping');
        


        $datacollection=$model->getCollection()
                        ->addFieldToFilter('customerid', $customerId)
                        ->addFieldToFilter('ruleset', $rule);
        if(count($datacollection) > 0){
            $this->_logger->info('customer found');
            if(strtolower($shppingvalue)=='rm-free'){
                // echo 'if';
                $this->_logger->info('customer found--rmfree');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('customerid', $customerId)
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', strtolower($shppingvalue));
                // print_r($datacollection->getData());
            }else{
                // echo 'else';
                $this->_logger->info('customer found--**');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('customerid', $customerId)
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', strtolower($shppingvalue));
                // print_r($datacollection->getData());
            }
            // echo count($datacollection);
            if (count($datacollection) <= 0){
                // echo 'lastif';
                $this->_logger->info('customer found--last');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('customerid', $customerId)
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', '*');
                if (count($datacollection) <= 0){
                    // echo 'lastif';
                    $this->_logger->info('customer found--last');
                    $datacollection=$model->getCollection()
                            ->addFieldToFilter('customerid', '0')
                            ->addFieldToFilter('ruleset', $rule)
                            ->addFieldToFilter('defaultshipping', '*');
                }
            }
            $data = $datacollection->getData();
            // print_r($data);
            foreach($data as $k=> $v){
                if($pairs<=$v['pairs']){
                    $shippingPrice = $v['magentoprice'];
                    $titlecustom = $v['magentomethod'];
                    $ipmethod = $v['ipmethod'];
                    break;
                }
            }
            
            
        }else{
            $this->_logger->info('customer not found');
            $datacollection=$model->getCollection()
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('pricing', strtolower($pricetype));
            if(strtolower($shppingvalue)=='rm-free'){
                // echo 'if';
                $this->_logger->info('customer not found-rmfree');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('pricing', strtolower($pricetype))
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', strtolower($shppingvalue));
                // print_r($datacollection->getData());
            }else{
                // echo 'else';
                $this->_logger->info('customer not found-**');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('pricing', strtolower($pricetype))
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', strtolower($shppingvalue));
                // print_r($datacollection->getData());
            }
            // echo count($datacollection);
            if (count($datacollection) <= 0){
                // echo 'lastif';
                $this->_logger->info('customer not found-last');
                $datacollection=$model->getCollection()
                        ->addFieldToFilter('pricing', strtolower($pricetype))
                        ->addFieldToFilter('ruleset', $rule)
                        ->addFieldToFilter('defaultshipping', '*');

                if (count($datacollection) <= 0){
                    // echo 'lastif';
                    $this->_logger->info('customer not found-last');
                    $datacollection=$model->getCollection()
                            ->addFieldToFilter('pricing', '*')
                            ->addFieldToFilter('ruleset', $rule)
                            ->addFieldToFilter('defaultshipping', '*');
                }
            }
            $data = $datacollection->getData();
            // print_r($data);
            // if($pairs<=$data[0]['pairs']){
            //     echo 'inside';
            // }
            foreach($data as $k=> $v){
                if($pairs<=$v['pairs']){
                    $shippingPrice = $v['magentoprice'];
                    $titlecustom = $v['magentomethod'];
                    $ipmethod = $v['ipmethod'];
                    break;
                }
            }
        }
       

        

        



        
        // $shippingPrice=0;
        if ($shippingPrice !== false) {

            $carriertitle='';

            $quote->setIsShippingmethod($ipmethod);
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */

            $method = $this->_rateMethodFactory->create();
            $method->setCarrier('flatrate');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('flatrate');

            $method->setMethodTitle($this->getConfigData('name'));

            // $shippingPrice = 80;
            // echo $shippingPrice;

            $method->setPrice($shippingPrice);

            $method->setCost($shippingPrice);
            $method->setMethodPrice($shippingPrice);

            $method->setMethodCost($shippingPrice);

            $method->setCarrierTitle($carriertitle);

            $method->setMethodTitle($titlecustom);
            
            // $quote->save();

            // $quote->setData('is_shippingmethod', $ipmethod); // Fill data

            // $this->quoteRepository->save($quote); // Save quote
            //$this->quoteRepository
            $varrr=$quote->getShippingAddress();
            // $shippingAddress->setCollectShippingRates(true)
            // ->collectShippingRates()
            // ->setShippingMethod('flatrate_flatrate');
            // $varrr->setCollectShippingRates(true)
                // ->collectShippingRates()
                // ->setShippingAmount($shippingPrice);
                
            // $quote->save();
            // echo "<pre>";
            // print_r($varrr->debug());
            
            // echo "<pre>";
            // var_dump($checkoutsession->getData());
            $quote->getShippingAddress()->setShippingAmount($shippingPrice);
            $quote->getShippingAddress()->setBaseShippingAmount($shippingPrice);
             $quote->save();
            //  echo "<pre>";
            // print_r($varrr->debug());

        }

        $result->append($method);

        //++++++++++++ custom code - Nitin edit Ends





        return $result;

    }



    /**

     * Get count of free boxes

     *

     * @param RateRequest $request

     * @return int

     */

    private function getFreeBoxesCount(RateRequest $request)

    {

        $freeBoxes = 0;

        if ($request->getAllItems()) {

            foreach ($request->getAllItems() as $item) {

                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {

                    continue;

                }



                if ($item->getHasChildren() && $item->isShipSeparately()) {

                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);

                } elseif ($item->getFreeShipping()) {

                    $freeBoxes += $item->getQty();

                }

            }

        }

        return $freeBoxes;

    }

    public function getShippingRules(RateRequest $request) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        foreach ($request->getAllItems() as $item) {
            $productId = $item->getProduct()->getId();
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            
            $this->_logger->info('getshippingRule'.$productId);
            $r=$product->getData('shipping_rule');
            $rule=$product->getResource()->getAttribute('shipping_rule')->getFrontend()->getValue($product);
            $this->_logger->info('getshippingRule->'.$rule.'->'.$r);
            // $rule=$item->getProduct()->getShippingRule();
            if(strtolower($rule)=='rule1') break;
        }
        return $rule;
    }



    /**

     * Get allowed shipping methods

     *

     * @return array

     */

    public function getAllowedMethods()

    {

        return [$this->_code => $this->getConfigData('name')];

    }



    /**

     * Returns shipping price

     *

     * @param RateRequest $request

     * @param int $freeBoxes

     * @return bool|float

     */

    private function getShippingPrice(RateRequest $request, $freeBoxes)

    {

        $shippingPrice = false;



        $configPrice = $this->getConfigData('price');

        if ($this->getConfigData('type') === 'O') {

            // per order

            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);

        } elseif ($this->getConfigData('type') === 'I') {

            // per item

            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);

        }



        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);



        if ($shippingPrice !== false && $request->getPackageQty() == $freeBoxes) {

            $shippingPrice = '0.00';

        }

        return $shippingPrice;

    }



    /**

     * Creates result method

     *

     * @param int|float $shippingPrice

     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method

     */

    private function createResultMethod($shippingPrice)

    {
        echo "<pre>";
        print_r($shippingPrice);

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */

        $method = $this->_rateMethodFactory->create();



        $method->setCarrier('flatrate');

        $method->setCarrierTitle($this->getConfigData('title'));



        $method->setMethod('flatrate');

        $method->setMethodTitle($this->getConfigData('name'));

        // $shippingPrice = 80;

        $method->setPrice($shippingPrice);

        $method->setCost($shippingPrice);

        return $method;

    }



    /**

     * Returns free boxes count of children

     *

     * @param mixed $item

     * @return mixed

     */

    private function getFreeBoxesCountFromChildren($item)

    {

        $freeBoxes = 0;

        foreach ($item->getChildren() as $child) {

            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {

                $freeBoxes += $item->getQty() * $child->getQty();

            }

        }

        return $freeBoxes;

    }

}