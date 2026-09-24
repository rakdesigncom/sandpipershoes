<?php
namespace Interprise\Logger\Block\Catalog;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Tierprice extends \Magento\Framework\View\Element\Template
{
    public $_session;
    protected $_registry;

    /**
     * Tierprice constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Registry $registry
     * @param \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster
     * @param \Interprise\Logger\Model\PricelistsFactory $pricelist
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Registry $registry,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        \Interprise\Logger\Model\PricelistsFactory $pricelist
    ) {
            $this->_registry = $registry;
            $this->_session = $session;
            $this->transactionmaster = $transactionmaster;
            $this->pricelist = $pricelist;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getTierprices()
    {
        $this->_session =  $this->_session->create();
        if ($this->_session->isLoggedIn()) {
            $is_pricinglevel = $this->_session->getISPricinglevel();
            $product = $this->getCurrentProduct();
            $unitmeasurecode = $product->getIsUnitmeasurecode();
            $is_itemcode = $product->getInterpriseItemCode();
            $pricelists = $this->pricelist->create();
            $collection = $pricelists->getCollection();
            $collection->addFieldToFilter('itemcode', ['eq' => $is_itemcode]);
            $collection->addFieldToFilter('pricelist', ['eq' => $is_pricinglevel]);
            $collection->addFieldToFilter('unitofmeasure', ['eq' => $unitmeasurecode]);
            $array = [];
            if ($collection->count() >0) {
                foreach ($collection as $k => $vals) {
                    $array[] = ['price'=>$vals['price'],'qty'=>$vals['to_qty']];
                }
            } else {
                return [];
            }
        } else {
              return [];
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    public function getCollectionorder()
    {
        $this->_session = $this->_session->create();
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $transaction_masters = $this->transactionmaster->create();
            $collection = $transaction_masters->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $collection->addFieldToFilter('customer_id', ['doc_type' => 'SO']);
            $this->setCollectionorder($collection);
            return $collection;
        } else {
             $this->_session->authenticate();
        }
        return false;
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Tierprice'));
        return $this;
    }
}
