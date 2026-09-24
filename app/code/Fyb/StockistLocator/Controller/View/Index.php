<?php

namespace Fyb\StockistLocator\Controller\View;

use Fyb\StockistLocator\Model\LocalstockistsFactory;
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
    protected $stockistFactory;
    protected $coreRegistry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Fyb\StockistLocator\Helper\Data $helper
     * @param \Fyb\StockistLocator\Model\StockistPageFactory $stockistFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Fyb\StockistLocator\Helper\Data $helper,
        \Fyb\StockistLocator\Model\StockistPageFactory $stockistFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
    ) {
        parent::__construct($context);

        $this->resultForwardFactory = $resultForwardFactory;
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->stockistFactory = $stockistFactory;
        $this->coreRegistry = $coreRegistry;
    }

    protected function initStockist()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $stockist = $this->stockistFactory->create()->load($id);

            if ($stockist->getId()) {
                $this->coreRegistry->register('current_stockist', $stockist);
                return $stockist;
            }
        }

        return null;
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $stockist = $this->initStockist();
        if (!$stockist || !$stockist->getActive()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        return $this->resultPageFactory->create();
    }
}
