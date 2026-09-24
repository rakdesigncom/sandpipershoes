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

namespace Interprise\Logger\Controller\Ccrm;

class Addnew extends \Magento\Framework\App\Action\Action
{
    public $_pageFactory;

    public $connection;

    protected $_session;

    protected $_datetime;

    protected $_casefact;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Interprise\Logger\Model\CasesFactory $casefactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_session = $session;
        $this->_datetime = $datetime;
        $this->_casefact = $casefactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = (array)$this->getRequest()->getPost();
        if (!empty($post)) {
            $subject = $post['subject'];
            $priority = $post['priority'];
            $problem = $post['problem'];
            $this->_session = $this->_session;
            if ($this->_session->isLoggedIn()) {
                $magentoDateObject = $this->_datetime;
                $date = $magentoDateObject->gmtDate();
                $customer_id = $this->_session->getData('customer_id');
                $model = $this->_casefact->create();
                $model->setData('case_number', 'Processing');
                $model->setData('subject', $subject);
                $model->setData('description', $problem);
                $model->setData('priority', $priority);
                $model->setData('customer_id', $customer_id);
                $model->setData('created_at', $date);
                $model->setData('updated_at', $date);
                $model->setData('status', 'pending');
                $model->save();
                $this->messageManager->addSuccess(__('Form successfully submitted'));
                $this->_redirect('icustomer/ccrm/index');
            }
        }

        return $this->_pageFactory->create();
    }
}
