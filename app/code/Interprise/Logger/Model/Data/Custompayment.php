<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\CustompaymentInterface;

class Custompayment extends \Magento\Framework\Api\AbstractExtensibleObject implements CustompaymentInterface
{

    /**
     * Get custompayment_id
     * @return string|null
     */
    public function getCustompaymentId()
    {
        return $this->_get(self::CUSTOMPAYMENT_ID);
    }

    /**
     * Set custompayment_id
     * @param string $custompaymentId
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCustompaymentId($custompaymentId)
    {
        return $this->setData(self::CUSTOMPAYMENT_ID, $custompaymentId);
    }

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CustompaymentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CustompaymentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CustompaymentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
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
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get unique_code
     * @return string|null
     */
    public function getUniqueCode()
    {
        return $this->_get(self::UNIQUE_CODE);
    }

    /**
     * Set unique_code
     * @param string $uniqueCode
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setUniqueCode($uniqueCode)
    {
        return $this->setData(self::UNIQUE_CODE, $uniqueCode);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get payment_method
     * @return string|null
     */
    public function getPaymentMethod()
    {
        return $this->_get(self::PAYMENT_METHOD);
    }

    /**
     * Set payment_method
     * @param string $paymentMethod
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setPaymentMethod($paymentMethod)
    {
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * Get is_receipt_no
     * @return string|null
     */
    public function getIsReceiptNo()
    {
        return $this->_get(self::IS_RECEIPT_NO);
    }

    /**
     * Set is_receipt_no
     * @param string $isReceiptNo
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setIsReceiptNo($isReceiptNo)
    {
        return $this->setData(self::IS_RECEIPT_NO, $isReceiptNo);
    }

    /**
     * Get receipt_status
     * @return string|null
     */
    public function getReceiptStatus()
    {
        return $this->_get(self::RECEIPT_STATUS);
    }

    /**
     * Set receipt_status
     * @param string $receiptStatus
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setReceiptStatus($receiptStatus)
    {
        return $this->setData(self::RECEIPT_STATUS, $receiptStatus);
    }
}
