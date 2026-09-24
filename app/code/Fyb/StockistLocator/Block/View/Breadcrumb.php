<?php

namespace Fyb\StockistLocator\Block\View;

class Breadcrumb extends \Fyb\StockistLocator\Block\View\AbstractStockist
{
    protected function _prepareLayout()
    {
        $stockist = $this->getStockist();

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'stockist',
                [
                    'label' => __($stockist->getName()),
                    'title' => __($stockist->getName()),
                ]
            );
        }

        $this->pageConfig->getTitle()->set(__($stockist->getName()));
        return parent::_prepareLayout();
    }
}
