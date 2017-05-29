<?php

namespace CustoMood\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectSetting
 *
 * @ORM\Table(name="project_setting")
 * @ORM\Entity(repositoryClass="CustoMood\Bundle\AppBundle\Repository\ProjectSettingRepository")
 */
class ProjectSetting extends SettingBase
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="settings")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * Set project
     *
     * @param \CustoMood\Bundle\AppBundle\Entity\Project $project
     *
     * @return ProjectSetting
     */
    public function setProject(\CustoMood\Bundle\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \CustoMood\Bundle\AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
