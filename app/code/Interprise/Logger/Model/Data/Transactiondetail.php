<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\TransactiondetailInterface;

class Transactiondetail extends \Magento\Framework\Api\AbstractExtensibleObject implements TransactiondetailInterface
{

    /**
     * Get transactiondetail_id
     * @return string|null
     */
    public function getTransactiondetailId()
    {
        return $this->_get(self::TRANSACTIONDETAIL_ID);
    }

    /**
     * Set transactiondetail_id
     * @param string $transactiondetailId
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setTransactiondetailId($transactiondetailId)
    {
        return $this->setData(self::TRANSACTIONDETAIL_ID, $transactiondetailId);
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
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\TransactiondetailExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setDocumentCode($documentCode)
    {
        return $this->setData(self::DOCUMENT_CODE, $documentCode);
    }

    /**
     * Get json_data
     * @return string|null
     */
    public function getJsonData()
    {
        return $this->_get(self::JSON_DATA);
    }

    /**
     * Set json_data
     * @param string $jsonData
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonData($jsonData)
    {
        return $this->setData(self::JSON_DATA, $jsonData);
    }

    /**
     * Get json_detail
     * @return string|null
     */
    public function getJsonDetail()
    {
        return $this->_get(self::JSON_DETAIL);
    }

    /**
     * Set json_detail
     * @param string $jsonDetail
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonDetail($jsonDetail)
    {
        return $this->setData(self::JSON_DETAIL, $jsonDetail);
    }

    /**
     * Get json_detail2
     * @return string|null
     */
    public function getJsonDetail2()
    {
        return $this->_get(self::JSON_DETAIL2);
    }

    /**
     * Set json_detail2
     * @param string $jsonDetail2
     * @return \Interprise\Logger\Api\Data\TransactiondetailInterface
     */
    public function setJsonDetail2($jsonDetail2)
    {
        return $this->setData(self::JSON_DETAIL2, $jsonDetail2);
    }
}
