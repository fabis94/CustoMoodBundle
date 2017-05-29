<?php

namespace CustoMood\Bundle\AppBundle\Service;

use CustoMood\Bundle\AppBundle\Entity\Setting;
use CustoMood\Bundle\AppBundle\Repository\SettingRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Read-only CM settings service
 *
 * Class SettingsService
 * @package CustoMood\Bundle\AppBundle\Service
 */
class SettingsService
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * SettingsService constructor.
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    /**
     * Get setting value by key
     * @param $key string
     * @return mixed
     */
    public function get($key) {
        /** @var Setting $setting */
        $setting = $this->getRepository()->find($key);
        return $setting != null ? $setting->getFormattedValue() : null;
    }

    /**
     * Get setting repository
     * @return SettingRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->manager->getRepository(Setting::class);
    }
}