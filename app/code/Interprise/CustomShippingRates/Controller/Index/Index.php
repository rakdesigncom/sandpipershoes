<?php



namespace Interprise\CustomShippingRates\Controller\Index;



class Index extends \Magento\Framework\App\Action\Action



{



    protected $_pageFactory;

    protected $_checkoutSession;

    protected $_coreSession;

    public function __construct(



       \Magento\Framework\App\Action\Context $context,

       \Magento\Checkout\Model\Session $checkoutSession,

       \Magento\Framework\Session\SessionManagerInterface $coreSession,

       \Magento\Framework\View\Result\PageFactory $pageFactory)



    {



       $this->_pageFactory = $pageFactory;

       $this->_checkoutSession = $checkoutSession;

       $this->_coreSession = $coreSession;

       return parent::__construct($context);



    }



    public function execute()



    {

      $post = $this->getRequest()->getPost('newaddid');

      $t=time();

      $this->_coreSession->start();
      $this->_coreSession->unsCustomerAddressCurrentId();
      $this->_coreSession->setCustomerAddressCurrentId($post.'-'.$t);

      // $this->_checkoutSession->setCustomerAddressCurrentId($post.'-'.$t);

      // echo $this->_checkoutSession->getCustomerAddressCurrentId();

      // sleep(2);

      return ;



    }

    public function test(){
      

      $id=$this->_coreSession->getCustomerAddressCurrentId();
      return $id;
   }



}