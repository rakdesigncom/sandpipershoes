<?php

namespace Fyb\StockistLocator\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Fyb\StockistLocator\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Fyb\StockistLocator\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Fyb\StockistLocator\Helper\Data $helper,
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
