<?php


namespace Interprise\Logger\Api\Data;

interface CronLogInterface
{

    const RESPONSE = 'Response';
    const STATUS = 'Status';
    const CRONMASTERID = 'CronMasterId';
    const RUNTIME = 'RunTime';
    const CRONLOG_ID = 'cronlog_id';
    const ACTIVITYCOUNT = 'ActivityCount';
    const REQUEST = 'Request';
    const REMARKS = 'Remarks';
    /**
     * Get cronlog_id
     * @return string|null
     */
    public function getCronlogId();

    /**
     * Set cronlog_id
     * @param string $cronlogId
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setCronlogId($cronlogId);

    /**
     * Get CronMasterId
     * @return string|null
     */
    public function getCronMasterId();

    /**
     * Set CronMasterId
     * @param string $cronMasterId
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setCronMasterId($cronMasterId);

    /**
     * Get RunTime
     * @return string|null
     */
    public function getRunTime();

    /**
     * Set RunTime
     * @param string $runTime
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setRunTime($runTime);

    /**
     * Get Request
     * @return string|null
     */
    public function getRequest();

    /**
     * Set Request
     * @param string $request
     * @return \Interprise\Logger\Api\Data\CronLogInterface
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
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setResponse($response);

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setStatus($status);

    /**
     * Get Remarks
     * @return string|null
     */
    public function getRemarks();

    /**
     * Set Remarks
     * @param string $remarks
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setRemarks($remarks);

    /**
     * Get ActivityCount
     * @return string|null
     */
    public function getActivityCount();

    /**
     * Set ActivityCount
     * @param string $activityCount
     * @return \Interprise\Logger\Api\Data\CronLogInterface
     */
    public function setActivityCount($activityCount);
}
