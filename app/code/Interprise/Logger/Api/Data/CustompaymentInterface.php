<?php


namespace Interprise\Logger\Api\Data;

interface CustompaymentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMPAYMENT_ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const AMOUNT = 'amount';
    const IS_RECEIPT_NO = 'is_receipt_no';
    const STATUS = 'status';
    const RECEIPT_STATUS = 'receipt_status';
    const PAYMENT_METHOD = 'payment_method';
    const CREATED_AT = 'created_at';
    const UNIQUE_CODE = 'unique_code';

    /**
     * Get custompayment_id
     * @return string|null
     */
    public function getCustompaymentId();

    /**
     * Set custompayment_id
     * @param string $custompaymentId
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCustompaymentId($custompaymentId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CustompaymentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CustompaymentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CustompaymentExtensionInterface $extensionAttributes
    );

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param string $amount
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setAmount($amount);

    /**
     * Get unique_code
     * @return string|null
     */
    public function getUniqueCode();

    /**
     * Set unique_code
     * @param string $uniqueCode
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setUniqueCode($uniqueCode);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setStatus($status);

    /**
     * Get payment_method
     * @return string|null
     */
    public function getPaymentMethod();

    /**
     * Set payment_method
     * @param string $paymentMethod
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Get is_receipt_no
     * @return string|null
     */
    public function getIsReceiptNo();

    /**
     * Set is_receipt_no
     * @param string $isReceiptNo
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setIsReceiptNo($isReceiptNo);

    /**
     * Get receipt_status
     * @return string|null
     */
    public function getReceiptStatus();

    /**
     * Set receipt_status
     * @param string $receiptStatus
     * @return \Interprise\Logger\Api\Data\CustompaymentInterface
     */
    public function setReceiptStatus($receiptStatus);
}
