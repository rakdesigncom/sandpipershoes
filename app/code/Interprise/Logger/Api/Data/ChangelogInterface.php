<?php


namespace Interprise\Logger\Api\Data;

interface ChangelogInterface
{

    const ITEMTYPE = 'ItemType';
    const CREATEDAT = 'CreatedAt';
    const JSONVALUE = 'JsonValue';
    const CHANGELOG_ID = 'changelog_id';
    const PUSHEDSTATUS = 'PushedStatus';
    const ITEMID = 'ItemId';
    const ACTION = 'Action';
    /**
     * Get changelog_id
     * @return string|null
     */
    public function getChangelogId();

    /**
     * Set changelog_id
     * @param string $changelogId
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setChangelogId($changelogId);

    /**
     * Get CreatedAt
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get ItemType
     * @return string|null
     */
    public function getItemType();

    /**
     * Set ItemType
     * @param string $itemType
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setItemType($itemType);

    /**
     * Get ItemId
     * @return string|null
     */
    public function getItemId();

    /**
     * Set ItemId
     * @param string $itemId
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setItemId($itemId);

    /**
     * Get Action
     * @return string|null
     */
    public function getAction();

    /**
     * Set Action
     * @param string $action
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setAction($action);

    /**
     * Get PushedStatus
     * @return string|null
     */
    public function getPushedStatus();

    /**
     * Set PushedStatus
     * @param string $pushedStatus
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setPushedStatus($pushedStatus);

    /**
     * Get JsonValue
     * @return string|null
     */
    public function getJsonValue();

    /**
     * Set JsonValue
     * @param string $jsonValue
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setJsonValue($jsonValue);
}
