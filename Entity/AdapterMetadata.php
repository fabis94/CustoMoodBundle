<?php

namespace CustoMood\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdapterMetadata
 *
 * @ORM\Table(name="adapter_metadata")
 * @ORM\Entity(repositoryClass="CustoMood\Bundle\AppBundle\Repository\AdapterMetadataRepository")
 */
class AdapterMetadata
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": true})
     */
    private $enabled;

    /**
     * AdapterMetadata constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return AdapterMetadata
     */
    public function setId(string $id): AdapterMetadata
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return AdapterMetadata
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}

