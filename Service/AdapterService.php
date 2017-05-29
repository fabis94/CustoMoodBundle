<?php
namespace CustoMood\Bundle\AppBundle\Service;

use CustoMood\Bundle\AppBundle\BaseAdapter\BaseAdapterInterface;
use CustoMood\Bundle\AppBundle\Entity\AdapterMetadata;
use CustoMood\Bundle\AppBundle\Repository\AdapterMetadataRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Finder\Finder;

/**
 * Service responsible for reading/parsing/integrating third-party adapters
 * Class AdapterService
 * @package CustoMood\Bundle\AppBundle\Service
 */
class AdapterService
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $adapters;

    /**
     * AdapterService constructor.
     * @param string $rootDir
     */
    public function __construct($rootDir, ManagerRegistry $doctrine)
    {
        $this->rootDir = $rootDir;
        $this->adapters = $this->findValidAdapters();
        $this->em = $doctrine->getManager();
    }

    /**
     * Returns if there are any enabled adapters enabled
     * @return bool
     */
    public function adaptersAvailable()
    {
        return count($this->getAdapters()) > 0;
    }

    /**
     * Get adapter by name
     *
     * @param string $name
     * @param bool $disabledAlso
     * @return BaseAdapterInterface|null
     */
    public function getAdapter(string $name, $disabledAlso = false)
    {
        if ($name == null || strlen($name) < 1)
            return null;

        foreach ($this->adapters as $class) {
            $id = $class::getId();
            if ($id == $name) {
                // Adapter found - does it need to be enabled?
                if (!$disabledAlso) {
                    $metadata = $this->getMetadata($id);
                    if ($metadata == null || $metadata->getEnabled()) {
                        return $class;
                    }
                } else {
                    return $class;
                }
            }
        }

        return null;
    }

    /**
     * Get all adapters
     * @param bool $findAll
     * @return array
     */
    public function getAdapters($findAll = false)
    {
        $adapters = $this->adapters;
        if (!$findAll) {
            $enabledAdapters = [];

            foreach ($adapters as $adapter) {
                $metadata = $this->getMetadata($adapter::getId());
                if ($metadata == null || $metadata->getEnabled())
                    $enabledAdapters[] = $adapter;
            }

            return $enabledAdapters;
        } else {
            return $adapters;
        }
    }

    /**
     * Reload adapters from files
     */
    public function reload()
    {
        $this->adapters = $this->findValidAdapters();
    }

    /**
     * Find AdapterMetadata by ID
     * @param $id
     * @return AdapterMetadata|null|object
     */
    protected function getMetadata($id)
    {
        return $this->getAdapterMetadataRepo()->find($id);
    }

    /**
     * Get adapter metadata repo
     * @return AdapterMetadataRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getAdapterMetadataRepo()
    {
        return $this->em->getRepository(AdapterMetadata::class);
    }

    /**
     * Get/Re-get all added third-party adapters
     * @return array
     */
    protected function findValidAdapters()
    {
        $adapterLocation = $this->getAdapterLocation();
        $finder = new Finder();
        $finder->files()->in($adapterLocation);
        $result = [];

        foreach ($finder as $file) {
            if ($file->getExtension() != 'php')
                continue;

            $fileName = $file->getBasename('.php');
            $className = $this->getAdapterNamespace() . '\\' . $fileName;

            $potentialAdapter = new $className(); // For some reason without instantiating, class_exists() sometimes fails
            if (!class_exists($className, false))
                continue;

            if (!is_subclass_of($className, BaseAdapterInterface::class))
                continue;

            // Class looks valid
            $result[] = $className;
        }

        return $result;
    }

    /**
     * Get absolute location of the main adapter classes
     * @return string
     */
    protected function getAdapterLocation()
    {
        $path = realpath($this->rootDir . '/../src/' . str_replace('\\', '/', $this->getAdapterNamespace()));
        return $path != false ? $path : null;
    }

    /**
     * Get namespace from which all adapters will be loaded
     * @return string
     */
    protected function getAdapterNamespace()
    {
        return "Adapters\ThirdParty";
    }
}