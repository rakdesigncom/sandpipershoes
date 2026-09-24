<?php


namespace Interprise\Logger\Api\Data;

interface CustompaymentitemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMPAYMENTITEM_ID = 'id';
    const CREATED_AT = 'created_at';
    const ITME_CODE = 'itme_code';
    const PAYMENT_ID = 'payment_id';
    const AMOUNT = 'amount';

    /**
     * Get custompaymentitem_id
     * @return string|null
     */
    public function getCustompaymentitemId();

    /**
     * Set custompaymentitem_id
     * @param string $custompaymentitemId
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setCustompaymentitemId($custompaymentitemId);

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Set payment_id
     * @param string $paymentId
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setPaymentId($paymentId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CustompaymentitemExtensionInterface $extensionAttributes
    );

    /**
     * Get itme_code
     * @return string|null
     */
    public function getItmeCode();

    /**
     * Set itme_code
     * @param string $itmeCode
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setItmeCode($itmeCode);

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param string $amount
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setAmount($amount);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\CustompaymentitemInterface
     */
    public function setCreatedAt($createdAt);
}
