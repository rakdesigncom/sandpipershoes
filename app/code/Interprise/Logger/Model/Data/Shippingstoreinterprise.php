<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Shippingstoreinterprise extends AbstractExtensibleObject implements ShippingstoreinterpriseInterface
{

    /**
     * Get shippingstoreinterprise_id
     * @return string|null
     */
    public function getShippingstoreinterpriseId()
    {
        return $this->_get(self::SHIPPINGSTOREINTERPRISE_ID);
    }

    /**
     * Set shippingstoreinterprise_id
     * @param string $shippingstoreinterpriseId
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setShippingstoreinterpriseId($shippingstoreinterpriseId)
    {
        return $this->setData(self::SHIPPINGSTOREINTERPRISE_ID, $shippingstoreinterpriseId);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get store_shipping_method
     * @return string|null
     */
    public function getStoreShippingMethod()
    {
        return $this->_get(self::STORE_SHIPPING_METHOD);
    }

    /**
     * Set store_shipping_method
     * @param string $storeShippingMethod
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setStoreShippingMethod($storeShippingMethod)
    {
        return $this->setData(self::STORE_SHIPPING_METHOD, $storeShippingMethod);
    }

    /**
     * Get interprise_shipping_method_code
     * @return string|null
     */
    public function getInterpriseShippingMethodCode()
    {
        return $this->_get(self::INTERPRISE_SHIPPING_METHOD_CODE);
    }

    /**
     * Set interprise_shipping_method_code
     * @param string $interpriseShippingMethodCode
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseShippingMethodCode($interpriseShippingMethodCode)
    {
        return $this->setData(self::INTERPRISE_SHIPPING_METHOD_CODE, $interpriseShippingMethodCode);
    }

    /**
     * Get used_country
     * @return string|null
     */
    public function getUsedCountry()
    {
        return $this->_get(self::USED_COUNTRY);
    }

    /**
     * Set used_country
     * @param string $usedCountry
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setUsedCountry($usedCountry)
    {
        return $this->setData(self::USED_COUNTRY, $usedCountry);
    }

    /**
     * Get interprise_country
     * @return string|null
     */
    public function getInterpriseCountry()
    {
        return $this->_get(self::INTERPRISE_COUNTRY);
    }

    /**
     * Set interprise_country
     * @param string $interpriseCountry
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseCountry($interpriseCountry)
    {
        return $this->setData(self::INTERPRISE_COUNTRY, $interpriseCountry);
    }

    /**
     * Get interprise_customer_class
     * @return string|null
     */
    public function getInterpriseCustomerClass()
    {
        return $this->_get(self::INTERPRISE_CUSTOMER_CLASS);
    }

    /**
     * Set interprise_customer_class
     * @param string $interpriseCustomerClass
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseCustomerClass($interpriseCustomerClass)
    {
        return $this->setData(self::INTERPRISE_CUSTOMER_CLASS, $interpriseCustomerClass);
    }

    /**
     * Get interprise_shipto_class
     * @return string|null
     */
    public function getInterpriseShiptoClass()
    {
        return $this->_get(self::INTERPRISE_SHIPTO_CLASS);
    }

    /**
     * Set interprise_shipto_class
     * @param string $interpriseShiptoClass
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseShiptoClass($interpriseShiptoClass)
    {
        return $this->setData(self::INTERPRISE_SHIPTO_CLASS, $interpriseShiptoClass);
    }
}
