<?php


namespace Interprise\Logger\Api\Data;

interface CronMasterInterface
{

    const CRON_STATUS = 'cron_status';
    const CRON_FROM_DATE = 'cron_from_date';
    const CRON_CHANGELOG_ENDPOINT = 'cron_changelog_endpoint';
    const CRON_NAME = 'cron_name';
    const CRON_ACTION = 'cron_action';
    const CRON_FREQUENCY = 'cron_frequency';
    const CRON_ACTIVE = 'cron_active';
    const CRON_FUNCTION = 'cron_function';
    const CRONMASTER_ID = 'cronmaster_id';
    const MASTER_ID =   'master_id';
    /**
     * Get cronmaster_id
     * @return string|null
     */
    public function getCronmasterId();

    /**
     * Set cronmaster_id
     * @param string $cronmasterId
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronmasterId($cronmasterId);
    public function setMasterId($masterId);
    public function getMasterId();

    /**
     * Get cron_name
     * @return string|null
     */
    public function getCronName();

    /**
     * Set cron_name
     * @param string $cronName
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronName($cronName);

    /**
     * Get cron_action
     * @return string|null
     */
    public function getCronAction();

    /**
     * Set cron_action
     * @param string $cronAction
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronAction($cronAction);

    /**
     * Get cron_function
     * @return string|null
     */
    public function getCronFunction();

    /**
     * Set cron_function
     * @param string $cronFunction
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronFunction($cronFunction);

    /**
     * Get cron_changelog_endpoint
     * @return string|null
     */
    public function getCronChangelogEndpoint();

    /**
     * Set cron_changelog_endpoint
     * @param string $cronChangelogEndpoint
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronChangelogEndpoint($cronChangelogEndpoint);

    /**
     * Get cron_frequency
     * @return string|null
     */
    public function getCronFrequency();

    /**
     * Set cron_frequency
     * @param string $cronFrequency
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronFrequency($cronFrequency);

    /**
     * Get cron_status
     * @return string|null
     */
    public function getCronStatus();

    /**
     * Set cron_status
     * @param string $cronStatus
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronStatus($cronStatus);

    /**
     * Get cron_active
     * @return string|null
     */
    public function getCronActive();

    /**
     * Set cron_active
     * @param string $cronActive
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronActive($cronActive);

    /**
     * Get cron_from_date
     * @return string|null
     */
    public function getCronFromDate();

    /**
     * Set cron_from_date
     * @param string $cronFromDate
     * @return \Interprise\Logger\Api\Data\CronMasterInterface
     */
    public function setCronFromDate($cronFromDate);
}
