<?php


namespace Interprise\Logger\Api\Data;

interface CasesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PRIORITY = 'priority';
    const DESCRIPTION = 'description';
    const CUSTOMER_ID = 'customer_id';
    const CASE_ID = 'entity_id';
    const STORE_ID = 'store_id';
    const SUBJECT = 'subject';
    const UPDATED_AT = 'updated_at';
    const END_AT = 'end_at';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const DUE_AT = 'due_at';
    const MAGENTO_CASE_NUMBER = 'magento_case_number';
    const CASE_NUMBER = 'case_number';

    /**
     * Get case_id
     * @return string|null
     */
    public function getCasesId();

    /**
     * Set case_id
     * @param string $caseId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCasesId($caseId);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setStoreId($storeId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CasesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CasesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CasesExtensionInterface $extensionAttributes
    );

    /**
     * Get case_number
     * @return string|null
     */
    public function getCasesNumber();

    /**
     * Set case_number
     * @param string $caseNumber
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCasesNumber($caseNumber);

    /**
     * Get subject
     * @return string|null
     */
    public function getSubject();

    /**
     * Set subject
     * @param string $subject
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setSubject($subject);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setDescription($description);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get due_at
     * @return string|null
     */
    public function getDueAt();

    /**
     * Set due_at
     * @param string $dueAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setDueAt($dueAt);

    /**
     * Get end_at
     * @return string|null
     */
    public function getEndAt();

    /**
     * Set end_at
     * @param string $endAt
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setEndAt($endAt);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setStatus($status);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get priority
     * @return string|null
     */
    public function getPriority();

    /**
     * Set priority
     * @param string $priority
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setPriority($priority);

    /**
     * Get magento_case_number
     * @return string|null
     */
    public function getMagentoCasesNumber();

    /**
     * Set magento_case_number
     * @param string $magentoCasesNumber
     * @return \Interprise\Logger\Api\Data\CasesInterface
     */
    public function setMagentoCasesNumber($magentoCasesNumber);
}
