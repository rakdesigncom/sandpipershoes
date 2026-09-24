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

use Magento\Framework\Controller\ResultFactory;

class Returns extends \Magento\Framework\App\Action\Action
{

    public $_pageFactory;

    public $connection;

    public $customer;

    public $_data_helper;

    public $_custompayment;

    protected $resultRedirect;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Customer\Model\Customer $customer,
        \Interprise\Logger\Helper\Data $data_helper,
        \Interprise\Logger\Model\CustompaymentFactory $custompayment,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->resultRedirect = $result;
        $this->customer = $customer;
        $this->_custompayment = $custompayment;
        $this->_data_helper = $data_helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $responce = $this->request->getParams();
        if (is_array($responce)
            && count(is_array($responce)) > 0
            && isset($responce['custom'])
            && $responce['custom'] != '') {
            try {
                $ids = $responce['custom'];
                $model = $this->_custompayment->create()->load($ids);
                $model->setData('status', 'success');
                $model->setData('payment_method', 'paypal');
                $model->save();

                $result_sel = $model->getData();
                if ($result_sel['customer_id']) {
                    $customer_id = $result_sel['customer_id'];
                }
                $customer_obj = $this->customer->load($customer_id);
                $customer_code = $customer_obj->getInterpriseCustomerCode();
                try {
                    $this->_data_helper->postReciptsInIs($result_sel, $customer_code);
                } catch (Exception $e) {
                    $this->_redirect('');
                }
            } catch (Exception $e) {
                $this->_redirect('');
            }
        } else {
            $this->_redirect('');
        }
        $this->messageManager->addSuccess(__('Transaction completed'));
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/account/index');

        return $resultRedirect;
    }
}
