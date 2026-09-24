<?php


namespace Interprise\Logger\Api\Data;

interface PaymentmethodInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const IS_PAYMENT_TERM_GROUP = 'is_payment_term_group';
    const IS_PAYMENT_TERM_CODE = 'is_payment_term_code';
    const PAYMENTMETHOD_ID = 'id';
    const DEFAULT_PAYMENT_METHOD = 'default_payment_method';
    const IS_ISACTIVE = 'is_isactive';
    const IS_PAYMENT_TYPE = 'is_payment_type';
    const IS_PAYMENT_TERM_DESCRIPTION = 'is_payment_term_description';
    const MAGENTO_METHOD = 'magento_method';

    /**
     * Get paymentmethod_id
     * @return string|null
     */
    public function getPaymentmethodId();

    /**
     * Set paymentmethod_id
     * @param string $paymentmethodId
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setPaymentmethodId($paymentmethodId);

    /**
     * Get is_payment_term_group
     * @return string|null
     */
    public function getIsPaymentTermGroup();

    /**
     * Set is_payment_term_group
     * @param string $isPaymentTermGroup
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermGroup($isPaymentTermGroup);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface $extensionAttributes
    );

    /**
     * Get is_payment_term_code
     * @return string|null
     */
    public function getIsPaymentTermCode();

    /**
     * Set is_payment_term_code
     * @param string $isPaymentTermCode
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermCode($isPaymentTermCode);

    /**
     * Get is_payment_type
     * @return string|null
     */
    public function getIsPaymentType();

    /**
     * Set is_payment_type
     * @param string $isPaymentType
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentType($isPaymentType);

    /**
     * Get is_payment_term_description
     * @return string|null
     */
    public function getIsPaymentTermDescription();

    /**
     * Set is_payment_term_description
     * @param string $isPaymentTermDescription
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermDescription($isPaymentTermDescription);

    /**
     * Get default_payment_method
     * @return string|null
     */
    public function getDefaultPaymentMethod();

    /**
     * Set default_payment_method
     * @param string $defaultPaymentMethod
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setDefaultPaymentMethod($defaultPaymentMethod);

    /**
     * Get is_isactive
     * @return string|null
     */
    public function getIsIsactive();

    /**
     * Set is_isactive
     * @param string $isIsactive
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsIsactive($isIsactive);

    /**
     * Get magento_method
     * @return string|null
     */
    public function getMagentoMethod();

    /**
     * Set magento_method
     * @param string $magentoMethod
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setMagentoMethod($magentoMethod);
}
