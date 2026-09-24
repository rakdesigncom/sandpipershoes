<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\PaymentmethodInterface;

class Paymentmethod extends \Magento\Framework\Api\AbstractExtensibleObject implements PaymentmethodInterface
{

    /**
     * Get paymentmethod_id
     * @return string|null
     */
    public function getPaymentmethodId()
    {
        return $this->_get(self::PAYMENTMETHOD_ID);
    }

    /**
     * Set paymentmethod_id
     * @param string $paymentmethodId
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setPaymentmethodId($paymentmethodId)
    {
        return $this->setData(self::PAYMENTMETHOD_ID, $paymentmethodId);
    }

    /**
     * Get is_payment_term_group
     * @return string|null
     */
    public function getIsPaymentTermGroup()
    {
        return $this->_get(self::IS_PAYMENT_TERM_GROUP);
    }

    /**
     * Set is_payment_term_group
     * @param string $isPaymentTermGroup
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermGroup($isPaymentTermGroup)
    {
        return $this->setData(self::IS_PAYMENT_TERM_GROUP, $isPaymentTermGroup);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PaymentmethodExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get is_payment_term_code
     * @return string|null
     */
    public function getIsPaymentTermCode()
    {
        return $this->_get(self::IS_PAYMENT_TERM_CODE);
    }

    /**
     * Set is_payment_term_code
     * @param string $isPaymentTermCode
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermCode($isPaymentTermCode)
    {
        return $this->setData(self::IS_PAYMENT_TERM_CODE, $isPaymentTermCode);
    }

    /**
     * Get is_payment_type
     * @return string|null
     */
    public function getIsPaymentType()
    {
        return $this->_get(self::IS_PAYMENT_TYPE);
    }

    /**
     * Set is_payment_type
     * @param string $isPaymentType
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentType($isPaymentType)
    {
        return $this->setData(self::IS_PAYMENT_TYPE, $isPaymentType);
    }

    /**
     * Get is_payment_term_description
     * @return string|null
     */
    public function getIsPaymentTermDescription()
    {
        return $this->_get(self::IS_PAYMENT_TERM_DESCRIPTION);
    }

    /**
     * Set is_payment_term_description
     * @param string $isPaymentTermDescription
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsPaymentTermDescription($isPaymentTermDescription)
    {
        return $this->setData(self::IS_PAYMENT_TERM_DESCRIPTION, $isPaymentTermDescription);
    }

    /**
     * Get default_payment_method
     * @return string|null
     */
    public function getDefaultPaymentMethod()
    {
        return $this->_get(self::DEFAULT_PAYMENT_METHOD);
    }

    /**
     * Set default_payment_method
     * @param string $defaultPaymentMethod
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setDefaultPaymentMethod($defaultPaymentMethod)
    {
        return $this->setData(self::DEFAULT_PAYMENT_METHOD, $defaultPaymentMethod);
    }

    /**
     * Get is_isactive
     * @return string|null
     */
    public function getIsIsactive()
    {
        return $this->_get(self::IS_ISACTIVE);
    }

    /**
     * Set is_isactive
     * @param string $isIsactive
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setIsIsactive($isIsactive)
    {
        return $this->setData(self::IS_ISACTIVE, $isIsactive);
    }

    /**
     * Get magento_method
     * @return string|null
     */
    public function getMagentoMethod()
    {
        return $this->_get(self::MAGENTO_METHOD);
    }

    /**
     * Set magento_method
     * @param string $magentoMethod
     * @return \Interprise\Logger\Api\Data\PaymentmethodInterface
     */
    public function setMagentoMethod($magentoMethod)
    {
        return $this->setData(self::MAGENTO_METHOD, $magentoMethod);
    }
}
