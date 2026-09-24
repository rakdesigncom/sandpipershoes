<?php


namespace Interprise\Logger\Api\Data;

interface StatementaccountInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const DOCUMENT_TYPE = 'document_type';
    const CUSTOMER_ID = 'customer_id';
    const TOTAL_RATE = 'total_rate';
    const REFERENCE = 'reference';
    const BALANCE_RATE = 'balance_rate';
    const STATEMENTACCOUNT_ID = 'statement_id';
    const INVOICE_CODE = 'invoice_code';
    const DUE_DATE = 'due_date';
    const DOCUMENT_DATE = 'document_date';

    /**
     * Get statementaccount_id
     * @return string|null
     */
    public function getStatementaccountId();

    /**
     * Set statementaccount_id
     * @param string $statementaccountId
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setStatementaccountId($statementaccountId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\StatementaccountExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\StatementaccountExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\StatementaccountExtensionInterface $extensionAttributes
    );

    /**
     * Get invoice_code
     * @return string|null
     */
    public function getInvoiceCode();

    /**
     * Set invoice_code
     * @param string $invoiceCode
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setInvoiceCode($invoiceCode);

    /**
     * Get document_date
     * @return string|null
     */
    public function getDocumentDate();

    /**
     * Set document_date
     * @param string $documentDate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDocumentDate($documentDate);

    /**
     * Get due_date
     * @return string|null
     */
    public function getDueDate();

    /**
     * Set due_date
     * @param string $dueDate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDueDate($dueDate);

    /**
     * Get document_type
     * @return string|null
     */
    public function getDocumentType();

    /**
     * Set document_type
     * @param string $documentType
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDocumentType($documentType);

    /**
     * Get balance_rate
     * @return string|null
     */
    public function getBalanceRate();

    /**
     * Set balance_rate
     * @param string $balanceRate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setBalanceRate($balanceRate);

    /**
     * Get total_rate
     * @return string|null
     */
    public function getTotalRate();

    /**
     * Set total_rate
     * @param string $totalRate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setTotalRate($totalRate);

    /**
     * Get reference
     * @return string|null
     */
    public function getReference();

    /**
     * Set reference
     * @param string $reference
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setReference($reference);
}
