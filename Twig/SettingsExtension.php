<?php

namespace CustoMood\Bundle\AppBundle\Twig;

use CustoMood\Bundle\AppBundle\Service\SettingsService;
use Twig_Extension;

class SettingsExtension extends Twig_Extension
{
    /**
     * @var SettingsService
     */
    protected $service;

    /**
     * SettingsExtension constructor.
     * @param SettingsService $service
     */
    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_setting_value', [$this, 'getSettingValue'])
        ];
    }

    /**
     * Get setting value or null if setting not found
     * @param $key
     * @return mixed
     */
    public function getSettingValue($key)
    {
        return $this->service->get($key);
    }
}