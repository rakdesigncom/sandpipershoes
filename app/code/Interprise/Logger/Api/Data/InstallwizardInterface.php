<?php


namespace Interprise\Logger\Api\Data;

interface InstallwizardInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TOTAL_RECORDS = 'total_records';
    const ACTION = 'action';
    const ITEM_NAME = 'item_name';
    const SORT_ORDER = 'sort_order';
    const FUNCTION_NAME = 'function_name';
    const STATUS = 'status';
    const SYNC_DONE = 'sync_done';
    const INSTALLWIZARD_ID = 'id';

    /**
     * Get installwizard_id
     * @return string|null
     */
    public function getInstallwizardId();

    /**
     * Set installwizard_id
     * @param string $installwizardId
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setInstallwizardId($installwizardId);

    /**
     * Get item_name
     * @return string|null
     */
    public function getItemName();

    /**
     * Set item_name
     * @param string $itemName
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setItemName($itemName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\InstallwizardExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\InstallwizardExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\InstallwizardExtensionInterface $extensionAttributes
    );

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setStatus($status);

    /**
     * Get action
     * @return string|null
     */
    public function getAction();

    /**
     * Set action
     * @param string $action
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setAction($action);

    /**
     * Get total_records
     * @return string|null
     */
    public function getTotalRecords();

    /**
     * Set total_records
     * @param string $totalRecords
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setTotalRecords($totalRecords);

    /**
     * Get function_name
     * @return string|null
     */
    public function getFunctionName();

    /**
     * Set function_name
     * @param string $functionName
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setFunctionName($functionName);

    /**
     * Get sync_done
     * @return string|null
     */
    public function getSyncDone();

    /**
     * Set sync_done
     * @param string $syncDone
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setSyncDone($syncDone);

    /**
     * Get sort_order
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setSortOrder($sortOrder);
}
