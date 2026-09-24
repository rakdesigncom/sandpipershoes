<?php


namespace Interprise\Logger\Api\Data;

interface CronActivityScheduleInterface
{

    const ISACTIVE = 'IsActive';
    const DATAID = 'DataId';
    const UPDATEDATE = 'UpdateDate';
    const CRONLOGID = 'CronLogId';
    const CRONACTIVITYSCHEDULE_ID = 'cronactivityschedule_id';
    const ACTIONTYPE = 'ActionType';
    const RESPONSE = 'Response';
    const STATUS = 'Status';
    const ITEMSTATUS = 'ItemStatus';
    const CRONMASTERID = 'CronMasterId';
    const SITE_ID = 'site_id';
    const ACTIVITYTIME = 'ActivityTime';
    const JSONDATA = 'JsonData';
    const REMARKS = 'Remarks';
    const REQUEST = 'Request';
    /**
     * Get cronactivityschedule_id
     * @return string|null
     */
    public function getCronactivityscheduleId();

    /**
     * Set cronactivityschedule_id
     * @param string $cronactivityscheduleId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setCronactivityscheduleId($cronactivityscheduleId);

    /**
     * Get CronLogId
     * @return string|null
     */
    public function getCronLogId();

    /**
     * Set CronLogId
     * @param string $cronLogId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setCronLogId($cronLogId);

    /**
     * Get CronMasterId
     * @return string|null
     */
    public function getCronMasterId();

    /**
     * Set CronMasterId
     * @param string $cronMasterId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setCronMasterId($cronMasterId);

    /**
     * Get ActionType
     * @return string|null
     */
    public function getActionType();

    /**
     * Set ActionType
     * @param string $actionType
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setActionType($actionType);

    /**
     * Get DataId
     * @return string|null
     */
    public function getDataId();

    /**
     * Set DataId
     * @param string $dataId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setDataId($dataId);

    /**
     * Get JsonData
     * @return string|null
     */
    public function getJsonData();

    /**
     * Set JsonData
     * @param string $jsonData
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setJsonData($jsonData);

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setStatus($status);

    /**
     * Get ActivityTime
     * @return string|null
     */
    public function getActivityTime();

    /**
     * Set ActivityTime
     * @param string $activityTime
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setActivityTime($activityTime);

    /**
     * Get Remarks
     * @return string|null
     */
    public function getRemarks();

    /**
     * Set Remarks
     * @param string $remarks
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setRemarks($remarks);

    /**
     * Get Request
     * @return string|null
     */
    public function getRequest();

    /**
     * Set Request
     * @param string $request
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setRequest($request);

    /**
     * Get Response
     * @return string|null
     */
    public function getResponse();

    /**
     * Set Response
     * @param string $response
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setResponse($response);

    /**
     * Get UpdateDate
     * @return string|null
     */
    public function getUpdateDate();

    /**
     * Set UpdateDate
     * @param string $updateDate
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setUpdateDate($updateDate);

    /**
     * Get IsActive
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set IsActive
     * @param string $isActive
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setIsActive($isActive);

    /**
     * Get ItemStatus
     * @return string|null
     */
    public function getItemStatus();

    /**
     * Set ItemStatus
     * @param string $itemStatus
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setItemStatus($itemStatus);

    /**
     * Get site_id
     * @return string|null
     */
    public function getSiteId();

    /**
     * Set site_id
     * @param string $siteId
     * @return \Interprise\Logger\Api\Data\CronActivityScheduleInterface
     */
    public function setSiteId($siteId);
}
