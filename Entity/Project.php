<?php

namespace CustoMood\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="CustoMood\Bundle\AppBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": true})
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime"))
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime"))
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="adapterId", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $adapterId;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ProjectSetting", mappedBy="project", cascade={"remove"})
     */
    private $settings;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->settings = new ArrayCollection();
        $this->enabled = true;
    }


    /**
     * Pre persist event handler
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = clone $this->createdAt;
    }

    /**
     * Pre update event handler
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Project
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

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Project
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set adapterId
     *
     * @param string $adapterId
     *
     * @return Project
     */
    public function setAdapterId($adapterId)
    {
        $this->adapterId = $adapterId;

        return $this;
    }

    /**
     * Get adapterId
     *
     * @return string
     */
    public function getAdapterId()
    {
        return $this->adapterId;
    }

    /**
     * Add setting
     *
     * @param \CustoMood\Bundle\AppBundle\Entity\ProjectSetting $setting
     *
     * @return Project
     */
    public function addSetting(\CustoMood\Bundle\AppBundle\Entity\ProjectSetting $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \CustoMood\Bundle\AppBundle\Entity\ProjectSetting $setting
     */
    public function removeSetting(\CustoMood\Bundle\AppBundle\Entity\ProjectSetting $setting)
    {
        $this->settings->removeElement($setting);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
