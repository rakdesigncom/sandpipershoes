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

namespace Interprise\Logger\Controller\Invoicepayment;

class Notify extends \Magento\Framework\App\Action\Action
{

    public $_pageFactory;

    public $_custompaymentfact;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_custompaymentfact = $custompaymentFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $responce = $this->request->getParams();
        $id = $responce['custom'];
        $model = $this->_custompaymentfact->create()->load($id);
        $model->setData('status', 'success');
        $model->setData('payment_method', 'paypal');
        $model->save();
    }
}
