<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\StatementaccountInterface;

class Statementaccount extends \Magento\Framework\Api\AbstractExtensibleObject implements StatementaccountInterface
{

    /**
     * Get statementaccount_id
     * @return string|null
     */
    public function getStatementaccountId()
    {
        return $this->_get(self::STATEMENTACCOUNT_ID);
    }

    /**
     * Set statementaccount_id
     * @param string $statementaccountId
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setStatementaccountId($statementaccountId)
    {
        return $this->setData(self::STATEMENTACCOUNT_ID, $statementaccountId);
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
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\StatementaccountExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\StatementaccountExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\StatementaccountExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get invoice_code
     * @return string|null
     */
    public function getInvoiceCode()
    {
        return $this->_get(self::INVOICE_CODE);
    }

    /**
     * Set invoice_code
     * @param string $invoiceCode
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setInvoiceCode($invoiceCode)
    {
        return $this->setData(self::INVOICE_CODE, $invoiceCode);
    }

    /**
     * Get document_date
     * @return string|null
     */
    public function getDocumentDate()
    {
        return $this->_get(self::DOCUMENT_DATE);
    }

    /**
     * Set document_date
     * @param string $documentDate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDocumentDate($documentDate)
    {
        return $this->setData(self::DOCUMENT_DATE, $documentDate);
    }

    /**
     * Get due_date
     * @return string|null
     */
    public function getDueDate()
    {
        return $this->_get(self::DUE_DATE);
    }

    /**
     * Set due_date
     * @param string $dueDate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDueDate($dueDate)
    {
        return $this->setData(self::DUE_DATE, $dueDate);
    }

    /**
     * Get document_type
     * @return string|null
     */
    public function getDocumentType()
    {
        return $this->_get(self::DOCUMENT_TYPE);
    }

    /**
     * Set document_type
     * @param string $documentType
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setDocumentType($documentType)
    {
        return $this->setData(self::DOCUMENT_TYPE, $documentType);
    }

    /**
     * Get balance_rate
     * @return string|null
     */
    public function getBalanceRate()
    {
        return $this->_get(self::BALANCE_RATE);
    }

    /**
     * Set balance_rate
     * @param string $balanceRate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setBalanceRate($balanceRate)
    {
        return $this->setData(self::BALANCE_RATE, $balanceRate);
    }

    /**
     * Get total_rate
     * @return string|null
     */
    public function getTotalRate()
    {
        return $this->_get(self::TOTAL_RATE);
    }

    /**
     * Set total_rate
     * @param string $totalRate
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setTotalRate($totalRate)
    {
        return $this->setData(self::TOTAL_RATE, $totalRate);
    }

    /**
     * Get reference
     * @return string|null
     */
    public function getReference()
    {
        return $this->_get(self::REFERENCE);
    }

    /**
     * Set reference
     * @param string $reference
     * @return \Interprise\Logger\Api\Data\StatementaccountInterface
     */
    public function setReference($reference)
    {
        return $this->setData(self::REFERENCE, $reference);
    }
}
