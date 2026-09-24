<?php
namespace Interprise\Logger\Block\Icustomer\Orderform;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;
    public $_pricingmagento;
    
    protected $_productCollectionFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccount,
        \Magento\Catalog\Block\Product\AbstractProduct $abstractProduct,
        array $data = []
    ) {
            $this->_session = $session;
            $this->_productCollectionFactory = $productCollectionFactory;
            $this->_statementaccount    = $statementaccount;
            $this->_abstractProduct    = $abstractProduct;
            parent::__construct($context, $data);
    }

    /**
     *
     */
    public function getCollectionorderform()
    {
        $this->_session = $this->_session->create();
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $collection = $this->_statementaccount->create();
            $collection = $collection->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $collection->setOrder('due_date', 'ASC');
            $this->setCollectionorderform($collection);
            return $collection;
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }
    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price)
    {
        $priceHelper = $this->pricehelper->create();
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Order Form'));
        return $this;
    }
    public function getProductCollection($cond = '')
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToFilter('type_id', ['eq' => 'simple']);
        $collection->addAttributeToSelect('description');
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addFinalPrice();
        
        if (!empty($cond)) {
            //$finalCond = array('like' => '%".$cond."%');
            //print_r($finalCond);
            $collection->addAttributeToFilter(
                [
                            [
                                'attribute' => 'name',
                                'like' => "%$cond%"
                            ],
                            [
                                'attribute' => 'sku',
                                'like' => "%$cond%"
                            ]
                ]
            );
        }
        return $collection;
    }
    public function createBlockNew()
    {
        return $this->_abstractProduct;
    }
}
