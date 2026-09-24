<?php


namespace Interprise\Logger\Api\Data;

interface TransactionmasterInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const SHIPTONAME = 'shiptoname';
    const DOC_TYPE = 'doc_type';
    const SOURCESALESORDERCODE = 'sourcesalesordercode';
    const CUSTOMER_ID = 'customer_id';
    const ROOTDOCUMENTCODE = 'rootdocumentcode';
    const DUEDATE = 'duedate';
    const PAYMENTTERMCODE = 'paymenttermcode';
    const TOTAL = 'total';
    const BALANCE = 'balance';
    const ISPOSTED = 'isposted';
    const SALESORDERDATE = 'salesorderdate';
    const TRANSACTIONMASTER_ID = 'transactionmaster_id';
    const UPDATED_AT = 'updated_at';
    const DOCUMENT_CODE = 'document_code';
    const STATUS = 'status';
    const POCODE = 'pocode';
    const ISVOIDED = 'isvoided';

    /**
     * Get transactionmaster_id
     * @return string|null
     */
    public function getTransactionmasterId();

    /**
     * Set transactionmaster_id
     * @param string $transactionmasterId
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setTransactionmasterId($transactionmasterId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface $extensionAttributes
    );

    /**
     * Get doc_type
     * @return string|null
     */
    public function getDocType();

    /**
     * Set doc_type
     * @param string $docType
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDocType($docType);

    /**
     * Get document_code
     * @return string|null
     */
    public function getDocumentCode();

    /**
     * Set document_code
     * @param string $documentCode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDocumentCode($documentCode);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get sourcesalesordercode
     * @return string|null
     */
    public function getSourcesalesordercode();

    /**
     * Set sourcesalesordercode
     * @param string $sourcesalesordercode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setSourcesalesordercode($sourcesalesordercode);

    /**
     * Get rootdocumentcode
     * @return string|null
     */
    public function getRootdocumentcode();

    /**
     * Set rootdocumentcode
     * @param string $rootdocumentcode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setRootdocumentcode($rootdocumentcode);

    /**
     * Get pocode
     * @return string|null
     */
    public function getPocode();

    /**
     * Set pocode
     * @param string $pocode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setPocode($pocode);

    /**
     * Get salesorderdate
     * @return string|null
     */
    public function getSalesorderdate();

    /**
     * Set salesorderdate
     * @param string $salesorderdate
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setSalesorderdate($salesorderdate);

    /**
     * Get duedate
     * @return string|null
     */
    public function getDuedate();

    /**
     * Set duedate
     * @param string $duedate
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDuedate($duedate);

    /**
     * Get shiptoname
     * @return string|null
     */
    public function getShiptoname();

    /**
     * Set shiptoname
     * @param string $shiptoname
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setShiptoname($shiptoname);

    /**
     * Get paymenttermcode
     * @return string|null
     */
    public function getPaymenttermcode();

    /**
     * Set paymenttermcode
     * @param string $paymenttermcode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setPaymenttermcode($paymenttermcode);

    /**
     * Get total
     * @return string|null
     */
    public function getTotal();

    /**
     * Set total
     * @param string $total
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setTotal($total);

    /**
     * Get balance
     * @return string|null
     */
    public function getBalance();

    /**
     * Set balance
     * @param string $balance
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setBalance($balance);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setStatus($status);

    /**
     * Get isposted
     * @return string|null
     */
    public function getIsposted();

    /**
     * Set isposted
     * @param string $isposted
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setIsposted($isposted);

    /**
     * Get isvoided
     * @return string|null
     */
    public function getIsvoided();

    /**
     * Set isvoided
     * @param string $isvoided
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setIsvoided($isvoided);
}
