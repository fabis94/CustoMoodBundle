<?php

namespace CustoMood\Bundle\AppBundle\Twig;

use CustoMood\Bundle\AppBundle\Service\AdapterService;

class AdapterExtension extends \Twig_Extension
{
    /**
     * @var AdapterService
     */
    protected $service;

    /**
     * AdapterExtension constructor.
     * @param AdapterService $service
     */
    public function __construct(AdapterService $service)
    {
        $this->service = $service;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('adapter_name', [$this, 'getAdapterName'])
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('adapters_available', [$this, 'anyAdaptersAvailable'])
        ];
    }

    /**
     * Returns whether there are any enabled adapters available
     * @return bool
     */
    public function anyAdaptersAvailable()
    {
        return $this->service->adaptersAvailable();
    }

    /**
     * Get adapter name by key
     * @param $key
     */
    public function getAdapterName($key)
    {
        $adapter = $this->service->getAdapter($key);
        return $adapter != null ? ($adapter::getDisplayName() ?: $key) : $key;
    }
}