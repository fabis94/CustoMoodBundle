<?php

namespace CustoMood\Bundle\AppBundle\Twig;

use CustoMood\Bundle\AppBundle\Entity\Project;
use CustoMood\Bundle\AppBundle\Service\AdapterService;
use Doctrine\Common\Persistence\ManagerRegistry;

class ProjectExtension extends \Twig_Extension
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $manager;

    /**
     * @var AdapterService
     */
    protected $adapterService;

    /**
     * SettingsService constructor.
     * @param ManagerRegistry $doctrine
     * @param AdapterService $adapterService
     */
    public function __construct(ManagerRegistry $doctrine, AdapterService $adapterService)
    {
        $this->manager = $doctrine->getManager();
        $this->adapterService = $adapterService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('projects', [$this, 'getProjects'])
        ];
    }

    /**
     * Get enabled/functioning projects
     */
    public function getProjects()
    {
        $results = [];
        $repo = $this->manager->getRepository(Project::class);
        $projects = $repo->findAllEnabled();
        if (count($projects) > 0) {
            // Check if enabled projects have working, enabled adapters
            foreach ($projects as $project) {
                $adapterId = $project->getAdapterId();
                $adapter = $this->adapterService->getAdapter($adapterId);

                if ($adapter != null) {
                    $results[] = $project;
                }
            }
        }

        return $results;
    }
}