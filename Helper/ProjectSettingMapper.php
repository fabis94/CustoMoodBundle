<?php

namespace CustoMood\Bundle\AppBundle\Helper;

use CustoMood\Bundle\AppBundle\Entity\ProjectSetting;

class ProjectSettingMapper
{
    /**
     * Create a single setting object from a single setting declaration array defined in BaseAdapterInterface
     * @param $settingDeclaration array
     * @return ProjectSetting
     */
    public static function createProjectSetting($settingDeclaration)
    {
        if (count($settingDeclaration) < 1)
            return null;

        $setting = new ProjectSetting();
        $setting
            ->setName($settingDeclaration['key'])
            ->setType($settingDeclaration['type'])
            ->setValue($settingDeclaration['value'])
            ->setDisplayName($settingDeclaration['display_name'])
            ->setRequired($settingDeclaration['required'])
            ->setSettingOrder($settingDeclaration['order']);

        return $setting;
    }

    /**
     * Create setting array from setting array defined in BaseAdapterInterface
     * @param $settingDeclarations
     * @return array
     */
    public static function createProjectSettings($settingDeclarations)
    {
        if (count($settingDeclarations) < 1)
            return [];

        $settings = [];
        foreach ($settingDeclarations as $settingDeclaration) {
            $entry = self::createProjectSetting($settingDeclaration);
            if ($entry != null)
                $settings[] = $entry;
        }
        return $settings;
    }

    /**
     * Create a key:value array from an array of ProjectSetting entities
     * @param $settings
     * @return array
     */
    public static function createKeyValueArray($settings)
    {
        if ($settings == null || count($settings) < 1)
            return [];

        $results = [];
        /** @var ProjectSetting $setting */
        foreach ($settings as $setting) {
            $results[$setting->getName()] = $setting->getFormattedValue();
        }

        return $results;
    }
}