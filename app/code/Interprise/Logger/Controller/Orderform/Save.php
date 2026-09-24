<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Index
 *
 * @author geuser1
 */

namespace Interprise\Logger\Controller\Orderform;

class Save extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;

    public $_cart;

    public $_product;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_cart = $cart;
        $this->_product = $productFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $quote = $this->_cart->create();
        $product = $this->getRequest()->getParam('productsadd');
        $i = 0;
        foreach ($product as $key) {
            if ($key['qty'] < 1) {
                continue;
            }
            $_product = $this->_product->create()->load($key['id']);
            if ($key['type'] == 'simple' || $key['type'] == 'virtual') {
                $params = [
                    'product' => $key['id'],
                    'qty' => $key['qty'],
                ];
            }
            if ($key['type'] == 'downloadable') {
                $params = [
                    'product' => $key['id'],
                    'qty' => $key['qty'],
                    'links' => $key['link'],
                ];
            }
            if ($key['type'] == 'configurable') {
                $params = [
                    'product' => $key['id'],
                    'qty' => $key['qty'],
                    'super_attribute' => $key['options'],
                ];
            }
            try {
                $quote->addProduct($_product, $params);
                $i++;
            } catch (Exception $ex) {
                continue;
            }
        }
        try {
            $quote->save();
        } catch (Exception $ex) {
            $this->messageManager->addNotice('Some error occured');
        }
        $this->messageManager->addSuccess(__("$i Products successfully added to cart"));
        $this->_redirect('icustomer/orderform/lists');
    }
}
