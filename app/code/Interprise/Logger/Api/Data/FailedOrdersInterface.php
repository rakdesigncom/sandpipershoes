<?php


namespace Interprise\Logger\Api\Data;

interface FailedOrdersInterface
{

    const REASON = 'Reason';
    const STATUS = 'Status';
    const INCREMENT_ID = 'Increment_id';
    const CHANGELOG_ITEM_ID = 'Changelog_item_id';
    const FAILEDORDERS_ID = 'failedorder_id';
    const CHANGELOG_ID = 'Changelog_id';
    const LAST_ATTEMPT = 'Last_attempt';
    const ATTEMPT_NO = 'Attempt_no';
    const NEXT_ATTEMPT = 'Next_attempt';
    /**
     * Get failedorder_id
     * @return string|null
     */
    public function getFailedOrdersId();

    /**
     * Set failedorder_id
     * @param string $failedordersId
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setFailedOrdersId($failedordersId);

    /**
     * Get IncrementId
     * @return string|null
     */
    public function getIncrementId();

    /**
     * Set IncrementId
     * @param string $incrementId
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Get ChangelogItemId
     * @return string|null
     */
    public function getChangelogItemId();

    /**
     * Set ChangelogItemId
     * @param string $changelogItemId
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setChangelogItemId($changelogItemId);

    /**
     * Get ChangelogId
     * @return string|null
     */
    public function getChangelogId();

    /**
     * Set ChangelogId
     * @param string $changelogId
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setChangelogId($changelogId);

    /**
     * Get Reason
     * @return string|null
     */
    public function getReason();

    /**
     * Set Reason
     * @param string $response
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setReason($response);

    /**
     * Get Status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     * @param string $status
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setStatus($status);

    /**
     * Get LastAttempt
     * @return string|null
     */
    public function getLastAttempt();

    /**
     * Set LastAttempt
     * @param string $lastAttempt
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setLastAttempt($lastAttempt);

    /**
     * Get NextAttempt
     * @return string|null
     */
    public function getNextAttempt();

    /**
     * Set NextAttempt
     * @param string $nextAttempt
     * @return \Interprise\Logger\Api\Data\FailedOrdersInterface
     */
    public function setNextAttempt($nextAttempt);
}
