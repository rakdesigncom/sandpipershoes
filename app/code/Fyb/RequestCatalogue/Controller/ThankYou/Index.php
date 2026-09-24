<?php

namespace Fyb\RequestCatalogue\Controller\ThankYou;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Fyb\RequestCatalogue\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Fyb\RequestCatalogue\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Fyb\RequestCatalogue\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultForwardFactory = $resultForwardFactory;
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            $result = $this->resultForwardFactory->create()->forward('noroute');
        } else {
            $result = $this->resultPageFactory->create();
        }

        return $result;
    }
}
