<?php

 

namespace Interprise\Logger\Plugin\Model;

use Magento\Framework\App\Area;

use Magento\Framework\App\State;

use Magento\Framework\App\PageCache\Version;

use Magento\Framework\App\Cache\TypeListInterface;

use Magento\Framework\App\Cache\Frontend\Pool;

use Magento\Customer\Api\CustomerRepositoryInterface;



class Product 

{

    /**

     * @var State

     */

    private $state;

    public $customerSession;



    public function __construct(

        State $state,

        TypeListInterface $cacheTypeList,

        Pool $cacheFrontendPool,

        \Magento\Customer\Model\Session $customerSession,

        \Magento\Store\Model\StoreManagerInterface $storeManager,

        CustomerRepositoryInterface $customerRepository

    ) {

        $this->customerSession = $customerSession; 

        $this->cacheTypeList = $cacheTypeList;

        $this->cacheFrontendPool = $cacheFrontendPool;

        $this->state = $state;

        $this->customerRepository = $customerRepository;

        $this->_storeManager = $storeManager;

        

    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)

    {   

        // if($his->getCustomerGroup==1)

         //print_r($subject->getID());

        // die;

        //echo '<br/>Inside afterGetPrice';

        //print_r($result);

        $om = \Magento\Framework\App\ObjectManager::getInstance(); 

        //$customerSession = $om->get('Magento\Customer\Model\Session'); 

        $customerData = $this->customerSession->getCustomer()->getData(); //get all data of customerData

        

        

       

        $customerId     =   $this->customerSession->getCustomer()->getId();

        $customerDataGid = $this->customerSession->getCustomer()->getGroupId();//get id of customer

        

        $logger = $om->get('\Interprise\Logger\Logger\Logger'); 

        $logger->info('Inside Customer Product Plugin');

                

        $productSKU = $subject->getSKU();

        $productID = $subject->getID();

        $productRetailPrice = $subject->getData('is_retailprice');
        $productWholesalePrice = $subject->getData('is_wholesaleprice');

        $logger->info('$productSKU'.$productSKU);
        $logger->info('$productRetailPrice'.$productRetailPrice);
        $logger->info('$productWholesalePrice'.$productWholesalePrice);

        $logger->info('productID '.$productID);

        $currencyCode = $this->getStoreCurrency();

        

        $basePrice = $result;

            

                

                $session = $om->get('\Magento\Customer\Model\SessionFactory')->create();

                //$discountBand = json_decode($session->getDiscountBand());

                //echo '<br/>priceList '.$session->getData();

                $customerId = $session->getId();

                

                $priceList = json_decode($session->getPriceList(), true);

                $logger->info('priceList '.$session->getPriceList());

                

                // echo '<pre>';

                // print_r($priceList);

                

                if(isset($priceList)){

                    if(count($priceList)>0){

                        //echo '<br/>Inside condition';

                        if(array_key_exists($productID, $priceList)){

                            if(array_key_exists($currencyCode, $priceList[$productID])){

                                foreach($priceList[$productID][$currencyCode] as $data){

                                    if($data['m']==1)

                                        $result = $data['p'];

                                    

                                }

                            }

                        }

                    }

                }



                if(isset($customerId) && $customerId!=''){

                    $customer_Data = $this->customerRepository->getById($customerId);
                    if($customer_Data->getCustomAttribute('interprise_defaultprice')){
                        $isDefaultPrice = $customer_Data->getCustomAttribute('interprise_defaultprice')->getValue();    

                        if(strtolower($isDefaultPrice)=='retail'){

                            $isDiscount = 0;

                            $isDiscountAttr = $customer_Data->getCustomAttribute('interprise_discount');

                            if(isset($isDiscountAttr))

                                $isDiscount = $isDiscountAttr->getValue();

                            if($isDiscount > 0){

                                
                                if($productRetailPrice!='' && $productRetailPrice > 0)
                                    $priceAfterDiscount = $productRetailPrice - ($productRetailPrice * $isDiscount/100);
                                else
                                    $priceAfterDiscount = $basePrice - ($basePrice * $isDiscount/100);

                                $result = $priceAfterDiscount;

                            }

                        } else if(strtolower($isDefaultPrice)=='wholesale'){

                            if($productWholesalePrice!='' && $productWholesalePrice>0)
                                $priceAfterDiscount = $productWholesalePrice;
                            else
                                $priceAfterDiscount = $basePrice;



                            $result = $priceAfterDiscount;

                        }
                    }

                }

                //print_r($result);

                //die;

                //print_r($discountBand);

                //print_r($priceList);

                

                return $result;

                //return round($result * 2.25);

            //}

            //else

                //return $result;

        }

        

       



    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)

    {   

        // if($his->getCustomerGroup==1)

        // var_dump($result);

        // die;

        //$logger->info('Inside afterGetSpecialPrice');

        //echo '<br/>Inside afterGetSpecialPrice';

        //print_r($result);

        $om = \Magento\Framework\App\ObjectManager::getInstance(); 

        $customerSession = $om->get('Magento\Customer\Model\Session'); 

        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData

        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer

        $product = $om->get('Magento\Framework\Registry')->registry('current_product');

        $attribute_set_name='';

        if(!empty($product)){

            $attributeSet = $om->create('Magento\Eav\Api\AttributeSetRepositoryInterface');

            $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());

            $attribute_set_name = $attributeSetRepository->getAttributeSetName();

        }

        

        $productID = $subject->getID();

        $productRetailPrice = $subject->getData('is_retailprice');
        $productWholesalePrice = $subject->getData('is_wholesaleprice');

        $currencyCode = $this->getStoreCurrency();

        if(!empty($result)){

            $basePrice = $result;

            //echo '<br/>Inside !empty(result)';

            //if($customerDataGid==2){

                //echo '<br/>$discountBand '.$discountBand = $customerSession->getDiscountBand();

                //print_r($customerSession->getCustomer()->getDiscountBand());

                //echo '<br/>'.$priceList = $customerSession->getPriceList();

                //echo '<br/>'.$subject->getID();



                $om = \Magento\Framework\App\ObjectManager::getInstance();  

                //$session1 = $om->get('Magento\Catalog\Model\Session');

                $session = $om->get('Magento\Customer\Model\SessionFactory')->create();

                //$discountBand = json_decode($session->getDiscountBand());

                $priceList = json_decode($session->getPriceList(), true);

                

                if(isset($priceList)){

                    if(count($priceList)>0){

                        //echo '<br/>Inside condition';

                        if(array_key_exists($productID, $priceList)){

                            if(array_key_exists($currencyCode, $priceList[$productID])){

                                foreach($priceList[$productID][$currencyCode] as $data){

                                    

                                    $result = $data['p'];

                                    

                                }

                            }

                        }

                    }

                }



                $customerId = $session->getId();

                if(isset($customerId) && $customerId!=''){

                    $customer_Data = $this->customerRepository->getById($customerId);
                    if($customer_Data->getCustomAttribute('interprise_defaultprice')){
                        $isDefaultPrice = $customer_Data->getCustomAttribute('interprise_defaultprice')->getValue();    

                        if(strtolower($isDefaultPrice)=='retail'){

                            $isDiscount = 0;

                            $isDiscountAttr = $customer_Data->getCustomAttribute('interprise_discount');

                            if(isset($isDiscountAttr))

                                $isDiscount = $isDiscountAttr->getValue();

                            if($isDiscount > 0){

                                if($productRetailPrice!='' && $productRetailPrice > 0)
                                    $priceAfterDiscount = $productRetailPrice - ($productRetailPrice * $isDiscount/100);
                                else
                                    $priceAfterDiscount = $basePrice - ($basePrice * $isDiscount/100);

                                $result = $priceAfterDiscount;

                            }

                        } else if(strtolower($isDefaultPrice)=='wholesale'){

                            if($productWholesalePrice!='' && $productWholesalePrice>0)
                                $priceAfterDiscount = $productWholesalePrice;
                            else
                                $priceAfterDiscount = $basePrice;



                            $result = $priceAfterDiscount;

                        }
                    }

                }



                return $result;

                //return round($result * 2.25);

            //}  else

            //    return $result;

        }

        //return 5.23;



    }



    public function afterGetTierPrice(\Magento\Catalog\Model\Product $subject, $result)

    {   

        

        //echo 'Inside afterGetTierPrice';

        $om = \Magento\Framework\App\ObjectManager::getInstance(); 

        $customerSession = $om->get('Magento\Customer\Model\Session'); 

        $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData

        $customerDataGid = $customerSession->getCustomer()->getGroupId();//get id of customer

        $product = $om->get('Magento\Framework\Registry')->registry('current_product');

        $productID = $subject->getID();

        $productRetailPrice = $subject->getData('is_retailprice');
        $productWholesalePrice = $subject->getData('is_wholesaleprice');

        $attribute_set_name='';

        if(!empty($product)){

            $attributeSet = $om->create('Magento\Eav\Api\AttributeSetRepositoryInterface');

            $attributeSetRepository = $attributeSet->get($product->getAttributeSetId());

            $attribute_set_name = $attributeSetRepository->getAttributeSetName();

        }

        

        $currencyCode = $this->getStoreCurrency();

        $basePrice = $result;

        //if($customerDataGid==2 && $attribute_set_name!='Gift Certificate'){

            

            //if($customerDataGid==2){

                //echo '<br/>$discountBand '.$discountBand = $customerSession->getDiscountBand();

                //print_r($customerSession->getCustomer()->getDiscountBand());

                //echo '<br/>'.$priceList = $customerSession->getPriceList();

                //echo '<br/>'.$subject->getID();

                $om = \Magento\Framework\App\ObjectManager::getInstance();  

                //$session1 = $om->get('Magento\Catalog\Model\Session');

                $session = $om->get('Magento\Customer\Model\SessionFactory')->create();

                //$discountBand = json_decode($session->getDiscountBand());

                $priceList = json_decode($session->getPriceList(), true);



                

                if(isset($priceList)){

                    if(count($priceList)>0){

                        //echo '<br/>Inside condition';

                        if(array_key_exists($productID, $priceList)){

                            if(array_key_exists($currencyCode, $priceList[$productID])){

                                foreach($priceList[$productID][$currencyCode] as $data){

                                    

                                    $result = $data['p'];

                                    

                                }

                            }

                        }

                    }

                }



                $customerId = $session->getId();

                if(isset($customerId) && $customerId!=''){

                    $customer_Data = $this->customerRepository->getById($customerId);
                    if($customer_Data->getCustomAttribute('interprise_defaultprice')){
                        $isDefaultPrice = $customer_Data->getCustomAttribute('interprise_defaultprice')->getValue();    

                        if(strtolower($isDefaultPrice)=='retail'){

                            $isDiscount = 0;

                            $isDiscountAttr = $customer_Data->getCustomAttribute('interprise_discount');

                            if(isset($isDiscountAttr))

                                $isDiscount = $isDiscountAttr->getValue();

                            if($isDiscount > 0){

                                if($productRetailPrice!='' && $productRetailPrice > 0)
                                    $priceAfterDiscount = $productRetailPrice - ($productRetailPrice * $isDiscount/100);
                                else
                                    $priceAfterDiscount = $basePrice - ($basePrice * $isDiscount/100);

                                $result = $priceAfterDiscount;

                            }

                        } else if(strtolower($isDefaultPrice)=='wholesale'){

                            if($productWholesalePrice!='' && $productWholesalePrice>0)
                                $priceAfterDiscount = $productWholesalePrice;
                            else
                                $priceAfterDiscount = $basePrice;



                            $result = $priceAfterDiscount;

                        }
                    }

                }

                return $result;

                //return round($result * 2.25);

            //}

        //} else

                //return $result;

        

    }



    public function cacheFunction()

    {

      $types = array('full_page');

     

        foreach ($types as $type) {

            $this->cacheTypeList->cleanType($type);

        }

        foreach ($this->cacheFrontendPool as $cacheFrontend) {

            $cacheFrontend->getBackend()->clean();

        }

    }



    /**

    * @return mixed

    */

    public function getStoreCurrency(){

        return $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();

        //return $this->localecurrency->getCurrency($currencycode)->getSymbol();

    }



    



    



}