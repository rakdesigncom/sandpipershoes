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

class Sagenotify extends \Magento\Framework\App\Action\Action
{

    public $_pageFactory;

    public $connection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $responce = $this->request->getParams();
        $id = $responce['custom'];
        $request = $this->getRequest();
        //$this->payid=$responce['PAY_ID'];
        //$this->payment='sagepay';
        //$this->save_data($responce);
        //$parameter=self::SuccessURL."?pay_id=".$helper->encrypt($this->payid);
        //$strResponse = 'Status=OK' . $this->eoln;
        //$strResponse .= 'StatusDetail=Transaction completed successfully' . $this->eoln;
        //$strResponse .= 'RedirectURL=' . $parameter. $this->eoln;
        //$this->getResponse()->setHeader('Content-type', 'text/plain');
        //$this->getResponse()->setBody($strResponse);
        return 0;
    }
}
