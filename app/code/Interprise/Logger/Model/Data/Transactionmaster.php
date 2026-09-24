<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\TransactionmasterInterface;

class Transactionmaster extends \Magento\Framework\Api\AbstractExtensibleObject implements TransactionmasterInterface
{

    /**
     * Get transactionmaster_id
     * @return string|null
     */
    public function getTransactionmasterId()
    {
        return $this->_get(self::TRANSACTIONMASTER_ID);
    }

    /**
     * Set transactionmaster_id
     * @param string $transactionmasterId
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setTransactionmasterId($transactionmasterId)
    {
        return $this->setData(self::TRANSACTIONMASTER_ID, $transactionmasterId);
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
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\TransactionmasterExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get doc_type
     * @return string|null
     */
    public function getDocType()
    {
        return $this->_get(self::DOC_TYPE);
    }

    /**
     * Set doc_type
     * @param string $docType
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDocType($docType)
    {
        return $this->setData(self::DOC_TYPE, $docType);
    }

    /**
     * Get document_code
     * @return string|null
     */
    public function getDocumentCode()
    {
        return $this->_get(self::DOCUMENT_CODE);
    }

    /**
     * Set document_code
     * @param string $documentCode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDocumentCode($documentCode)
    {
        return $this->setData(self::DOCUMENT_CODE, $documentCode);
    }

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get sourcesalesordercode
     * @return string|null
     */
    public function getSourcesalesordercode()
    {
        return $this->_get(self::SOURCESALESORDERCODE);
    }

    /**
     * Set sourcesalesordercode
     * @param string $sourcesalesordercode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setSourcesalesordercode($sourcesalesordercode)
    {
        return $this->setData(self::SOURCESALESORDERCODE, $sourcesalesordercode);
    }

    /**
     * Get rootdocumentcode
     * @return string|null
     */
    public function getRootdocumentcode()
    {
        return $this->_get(self::ROOTDOCUMENTCODE);
    }

    /**
     * Set rootdocumentcode
     * @param string $rootdocumentcode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setRootdocumentcode($rootdocumentcode)
    {
        return $this->setData(self::ROOTDOCUMENTCODE, $rootdocumentcode);
    }

    /**
     * Get pocode
     * @return string|null
     */
    public function getPocode()
    {
        return $this->_get(self::POCODE);
    }

    /**
     * Set pocode
     * @param string $pocode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setPocode($pocode)
    {
        return $this->setData(self::POCODE, $pocode);
    }

    /**
     * Get salesorderdate
     * @return string|null
     */
    public function getSalesorderdate()
    {
        return $this->_get(self::SALESORDERDATE);
    }

    /**
     * Set salesorderdate
     * @param string $salesorderdate
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setSalesorderdate($salesorderdate)
    {
        return $this->setData(self::SALESORDERDATE, $salesorderdate);
    }

    /**
     * Get duedate
     * @return string|null
     */
    public function getDuedate()
    {
        return $this->_get(self::DUEDATE);
    }

    /**
     * Set duedate
     * @param string $duedate
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setDuedate($duedate)
    {
        return $this->setData(self::DUEDATE, $duedate);
    }

    /**
     * Get shiptoname
     * @return string|null
     */
    public function getShiptoname()
    {
        return $this->_get(self::SHIPTONAME);
    }

    /**
     * Set shiptoname
     * @param string $shiptoname
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setShiptoname($shiptoname)
    {
        return $this->setData(self::SHIPTONAME, $shiptoname);
    }

    /**
     * Get paymenttermcode
     * @return string|null
     */
    public function getPaymenttermcode()
    {
        return $this->_get(self::PAYMENTTERMCODE);
    }

    /**
     * Set paymenttermcode
     * @param string $paymenttermcode
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setPaymenttermcode($paymenttermcode)
    {
        return $this->setData(self::PAYMENTTERMCODE, $paymenttermcode);
    }

    /**
     * Get total
     * @return string|null
     */
    public function getTotal()
    {
        return $this->_get(self::TOTAL);
    }

    /**
     * Set total
     * @param string $total
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setTotal($total)
    {
        return $this->setData(self::TOTAL, $total);
    }

    /**
     * Get balance
     * @return string|null
     */
    public function getBalance()
    {
        return $this->_get(self::BALANCE);
    }

    /**
     * Set balance
     * @param string $balance
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
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
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get isposted
     * @return string|null
     */
    public function getIsposted()
    {
        return $this->_get(self::ISPOSTED);
    }

    /**
     * Set isposted
     * @param string $isposted
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setIsposted($isposted)
    {
        return $this->setData(self::ISPOSTED, $isposted);
    }

    /**
     * Get isvoided
     * @return string|null
     */
    public function getIsvoided()
    {
        return $this->_get(self::ISVOIDED);
    }

    /**
     * Set isvoided
     * @param string $isvoided
     * @return \Interprise\Logger\Api\Data\TransactionmasterInterface
     */
    public function setIsvoided($isvoided)
    {
        return $this->setData(self::ISVOIDED, $isvoided);
    }
}
