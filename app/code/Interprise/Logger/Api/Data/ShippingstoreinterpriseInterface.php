<?php


namespace Interprise\Logger\Api\Data;

interface ShippingstoreinterpriseInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const STORE_SHIPPING_METHOD = 'store_shipping_method';
    const INTERPRISE_SHIPTO_CLASS = 'interprise_shipto_class';
    const STORE_ID = 'store_id';
    const SHIPPINGSTOREINTERPRISE_ID = 'id';
    const USED_COUNTRY = 'used_country';
    const INTERPRISE_CUSTOMER_CLASS = 'interprise_customer_class';
    const INTERPRISE_SHIPPING_METHOD_CODE = 'interprise_shipping_method_code';
    const INTERPRISE_COUNTRY = 'interprise_country';

    /**
     * Get shippingstoreinterprise_id
     * @return string|null
     */
    public function getShippingstoreinterpriseId();

    /**
     * Set shippingstoreinterprise_id
     * @param string $shippingstoreinterpriseId
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setShippingstoreinterpriseId($shippingstoreinterpriseId);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setStoreId($storeId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\ShippingstoreinterpriseExtensionInterface $extensionAttributes
    );

    /**
     * Get store_shipping_method
     * @return string|null
     */
    public function getStoreShippingMethod();

    /**
     * Set store_shipping_method
     * @param string $storeShippingMethod
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setStoreShippingMethod($storeShippingMethod);

    /**
     * Get interprise_shipping_method_code
     * @return string|null
     */
    public function getInterpriseShippingMethodCode();

    /**
     * Set interprise_shipping_method_code
     * @param string $interpriseShippingMethodCode
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseShippingMethodCode($interpriseShippingMethodCode);

    /**
     * Get used_country
     * @return string|null
     */
    public function getUsedCountry();

    /**
     * Set used_country
     * @param string $usedCountry
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setUsedCountry($usedCountry);

    /**
     * Get interprise_country
     * @return string|null
     */
    public function getInterpriseCountry();

    /**
     * Set interprise_country
     * @param string $interpriseCountry
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseCountry($interpriseCountry);

    /**
     * Get interprise_customer_class
     * @return string|null
     */
    public function getInterpriseCustomerClass();

    /**
     * Set interprise_customer_class
     * @param string $interpriseCustomerClass
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseCustomerClass($interpriseCustomerClass);

    /**
     * Get interprise_shipto_class
     * @return string|null
     */
    public function getInterpriseShiptoClass();

    /**
     * Set interprise_shipto_class
     * @param string $interpriseShiptoClass
     * @return \Interprise\Logger\Api\Data\ShippingstoreinterpriseInterface
     */
    public function setInterpriseShiptoClass($interpriseShiptoClass);
}
