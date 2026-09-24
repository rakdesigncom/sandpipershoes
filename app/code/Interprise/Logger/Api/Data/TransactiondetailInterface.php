<?php


namespace Interprise\Logger\Api\Data;

interface TransactiondetailInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMER_ID = 'customer_id';
    const JSON_DATA = 'json_data';
    const DOCUMENT_CODE = 'document_code';
    const TRANSACTIONDETAIL_ID = 'id';
    const JSON_DETAIL = 'json_detail';
    const JSON_DETAIL2 = 'json_detail2';

    /**
     * Get transactiondetail_id
     * @return string|null
     */
    public function getTransactiondetailId();

    /**
     * Set transactiondetail_id
     * @param string $transactiondetailId
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setTransactiondetailId($transactiondetailId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface $extensionAttributes
    );

    /**
     * Get document_code
     * @return string|null
     */
    public function getDocumentCode();

    /**
     * Set document_code
     * @param string $documentCode
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setDocumentCode($documentCode);

    /**
     * Get json_data
     * @return string|null
     */
    public function getJsonData();

    /**
     * Set json_data
     * @param string $jsonData
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonData($jsonData);

    /**
     * Get json_detail
     * @return string|null
     */
    public function getJsonDetail();

    /**
     * Set json_detail
     * @param string $jsonDetail
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonDetail($jsonDetail);

    /**
     * Get json_detail2
     * @return string|null
     */
    public function getJsonDetail2();

    /**
     * Set json_detail2
     * @param string $jsonDetail2
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonDetail2($jsonDetail2);
}
