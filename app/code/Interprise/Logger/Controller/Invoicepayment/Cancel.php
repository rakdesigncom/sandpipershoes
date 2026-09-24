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

class Cancel extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;

    protected $resultRedirect;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->resultRedirect = $result;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->messageManager->addNotice(__('Transaction cancelled'));
        $this->_redirect('icustomer/invoicepayment/index');
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('icustomer/invoicepayment/index');

        return $resultRedirect;
    }
}
