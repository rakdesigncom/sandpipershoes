<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\InstallwizardInterface;

class Installwizard extends \Magento\Framework\Api\AbstractExtensibleObject implements InstallwizardInterface
{

    /**
     * Get installwizard_id
     * @return string|null
     */
    public function getInstallwizardId()
    {
        return $this->_get(self::INSTALLWIZARD_ID);
    }

    /**
     * Set installwizard_id
     * @param string $installwizardId
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setInstallwizardId($installwizardId)
    {
        return $this->setData(self::INSTALLWIZARD_ID, $installwizardId);
    }

    /**
     * Get item_name
     * @return string|null
     */
    public function getItemName()
    {
        return $this->_get(self::ITEM_NAME);
    }

    /**
     * Set item_name
     * @param string $itemName
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setItemName($itemName)
    {
        return $this->setData(self::ITEM_NAME, $itemName);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\InstallwizardExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\InstallwizardExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\InstallwizardExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get action
     * @return string|null
     */
    public function getAction()
    {
        return $this->_get(self::ACTION);
    }

    /**
     * Set action
     * @param string $action
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Get total_records
     * @return string|null
     */
    public function getTotalRecords()
    {
        return $this->_get(self::TOTAL_RECORDS);
    }

    /**
     * Set total_records
     * @param string $totalRecords
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setTotalRecords($totalRecords)
    {
        return $this->setData(self::TOTAL_RECORDS, $totalRecords);
    }

    /**
     * Get function_name
     * @return string|null
     */
    public function getFunctionName()
    {
        return $this->_get(self::FUNCTION_NAME);
    }

    /**
     * Set function_name
     * @param string $functionName
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setFunctionName($functionName)
    {
        return $this->setData(self::FUNCTION_NAME, $functionName);
    }

    /**
     * Get sync_done
     * @return string|null
     */
    public function getSyncDone()
    {
        return $this->_get(self::SYNC_DONE);
    }

    /**
     * Set sync_done
     * @param string $syncDone
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setSyncDone($syncDone)
    {
        return $this->setData(self::SYNC_DONE, $syncDone);
    }

    /**
     * Get sort_order
     * @return string|null
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return \Interprise\Logger\Api\Data\InstallwizardInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }
}
