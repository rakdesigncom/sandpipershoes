<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\CustompaymentitemInterface;

class Custompaymentitem extends \Magento\Framework\Api\AbstractExtensibleObject implements CustompaymentitemInterface
{

    /**
     * Get custompaymentitem_id
     * @return string|null
     */
    public function getCustompaymentitemId()
    {
        return $this->_get(self::CUSTOMPAYMENTITEM_ID);
    }

    /**
     * Set custompaymentitem_id
     * @param string $custompaymentitemId
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setCustompaymentitemId($custompaymentitemId)
    {
        return $this->setData(self::CUSTOMPAYMENTITEM_ID, $custompaymentitemId);
    }

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId()
    {
        return $this->_get(self::PAYMENT_ID);
    }

    /**
     * Set payment_id
     * @param string $paymentId
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setPaymentId($paymentId)
    {
        return $this->setData(self::PAYMENT_ID, $paymentId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get itme_code
     * @return string|null
     */
    public function getItmeCode()
    {
        return $this->_get(self::ITME_CODE);
    }

    /**
     * Set itme_code
     * @param string $itmeCode
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setItmeCode($itmeCode)
    {
        return $this->setData(self::ITME_CODE, $itmeCode);
    }

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * Set amount
     * @param string $amount
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
