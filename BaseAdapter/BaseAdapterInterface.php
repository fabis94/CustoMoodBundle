<?php

namespace CustoMood\Bundle\AppBundle\BaseAdapter;

interface BaseAdapterInterface
{
    /**
     * Unique ID of this adapter
     * @return string
     */
    public static function getId(): string;

    /**
     * Display name that will be used in CM (can be null, in which case the ID will be used)
     * @return string
     */
    public static function getDisplayName(): string;

    /**
     * Author
     * @return string
     */
    public static function getAuthor(): string;

    /**
     * Description (can be null)
     * @return string
     */
    public static function getDescription(): string;

    /**
     * Author's website (can be null)
     * @return string
     */
    public static function getWebsite(): string;

    /**
     * Version
     * @return float
     */
    public static function getVersion(): float;

    /**
     * If needed, define any settings that your adapter needs here
     * A single array entry should be structured like this:
     * [
     *      'key' => 'my_first_setting', // Key/Name of the setting. This should be unique within your settings
     *      'value' => '0', // Default value
     *      'display_name' => 'My First Setting',
     *      'type' => 2, // 0 = string, 1 = integer, 2 = boolean
     *      'required' => false, // True, if user has to fill this out
     *      'order' => 1, // Order of this setting within your other settings
     * ]
     * @return array
     */
    public static function getSettingsSchema(): array;

    /**
     * This will get called on load and will be filled with the projectId and also values to the settings defined in getSettingsSchema()
     * @param $projectId string
     * @param $settingValues array
     * @return mixed
     */
    public function load($projectId, $settingValues);

    /**
     * This should return an array of client comments between the two dates. Each array item should follow this format:
     * [
     *      'date' => an integer UNIX timestamp of the comment,
     *      'text' => the actual comment
     * ]
     * @param \DateTime $dateFrom Date from which to get data
     * @param \DateTime $dateTo Date till which to get data
     * @return mixed
     */
    public function getMood($dateFrom, $dateTo): array;
}