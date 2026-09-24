<?php
namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\CasesInterface;

class Cases extends \Magento\Framework\Api\AbstractExtensibleObject implements CasesInterface
{

    /**
     * Get case_id
     * @return string|null
     */
    public function getCasesId()
    {
        return $this->_get(self::CASE_ID);
    }

    /**
     * Set case_id
     * @param string $caseId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCasesId($caseId)
    {
        return $this->setData(self::CASE_ID, $caseId);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CasesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CasesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CasesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get case_number
     * @return string|null
     */
    public function getCasesNumber()
    {
        return $this->_get(self::CASE_NUMBER);
    }

    /**
     * Set case_number
     * @param string $caseNumber
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCasesNumber($caseNumber)
    {
        return $this->setData(self::CASE_NUMBER, $caseNumber);
    }

    /**
     * Get subject
     * @return string|null
     */
    public function getSubject()
    {
        return $this->_get(self::SUBJECT);
    }

    /**
     * Set subject
     * @param string $subject
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
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
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
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
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get due_at
     * @return string|null
     */
    public function getDueAt()
    {
        return $this->_get(self::DUE_AT);
    }

    /**
     * Set due_at
     * @param string $dueAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setDueAt($dueAt)
    {
        return $this->setData(self::DUE_AT, $dueAt);
    }

    /**
     * Get end_at
     * @return string|null
     */
    public function getEndAt()
    {
        return $this->_get(self::END_AT);
    }

    /**
     * Set end_at
     * @param string $endAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setEndAt($endAt)
    {
        return $this->setData(self::END_AT, $endAt);
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
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get priority
     * @return string|null
     */
    public function getPriority()
    {
        return $this->_get(self::PRIORITY);
    }

    /**
     * Set priority
     * @param string $priority
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get magento_case_number
     * @return string|null
     */
    public function getMagentoCasesNumber()
    {
        return $this->_get(self::MAGENTO_CASE_NUMBER);
    }

    /**
     * Set magento_case_number
     * @param string $magentoCasesNumber
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setMagentoCasesNumber($magentoCasesNumber)
    {
        return $this->setData(self::MAGENTO_CASE_NUMBER, $magentoCasesNumber);
    }
}
