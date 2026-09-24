<?php


namespace Interprise\Logger\Api\Data;

interface ReattemptFrequencyInterface
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
    public function getReattemptId();

    /**
     * Set failedorder_id
     * @param string $failedordersId
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencyInterface
     */
    public function setReattemptId($reattemptId);

    /**
     * Get IncrementId
     * @return string|null
     */
    public function getAttemptNo();

    /**
     * Set IncrementId
     * @param string $incrementId
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencyInterface
     */
    public function setAttemptNo($attemptId);

    /**
     * Get ChangelogItemId
     * @return string|null
     */
    public function getInterval();

    /**
     * Set ChangelogItemId
     * @param string $changelogItemId
     * @return \Interprise\Logger\Api\Data\ReattemptFrequencyInterface
     */
    public function setInterval($interval);

    
}
