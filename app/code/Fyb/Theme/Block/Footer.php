<?php

namespace Fyb\Theme\Block;

class Footer extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\Information $storeInfo,
        \Magento\Theme\Block\Html\Header\Logo $_logo,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->storeInfo = $storeInfo;
        $this->_logo = $_logo;
    }

    public function getStorePhone()
    {
        return $this->getStoreInformationObject()->getData('phone');
    }

    /**
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreInformationObject()
    {
        return $this->storeInfo->getStoreInformationObject($this->_storeManager->getStore());
    }

    public function getStoreShortAddress()
    {
        $storeData = $this->getStoreInformationObject();

        $address = [
            $storeData->getData('street_line1'),
            $storeData->getData('street_line2'),
            $storeData->getData('city'),
            $storeData->getPostcode(),
        ];

        return implode(', ', array_filter($address));
    }

    public function getLogo()
    {
        return $this->_logo->getLogoSrc();
    }
}
