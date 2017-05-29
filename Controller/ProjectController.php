<?php

namespace CustoMood\Bundle\AppBundle\Controller;

use CustoMood\Bundle\AppBundle\DBAL\Types\SettingType;
use CustoMood\Bundle\AppBundle\Entity\Project;
use CustoMood\Bundle\AppBundle\Entity\ProjectSetting;
use CustoMood\Bundle\AppBundle\Form\ProjectType;
use CustoMood\Bundle\AppBundle\Helper\ProjectSettingMapper;
use CustoMood\Bundle\AppBundle\Repository\ProjectRepository;
use CustoMood\Bundle\AppBundle\Service\AdapterService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/project")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/", name="admin_project_all")
     */
    public function allAction(Request $request)
    {
        $projectRepo = $this->getProjectRepo();
        $projects = $projectRepo->findAll();

        return $this->render('CustoMoodAppBundle::Admin/Project/all.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * @Route("/delete/{pid}", name="admin_project_delete", requirements={"page": "\d+"})
     */
    public function deleteAction($pid)
    {
        if ($pid != null) {
            $repo = $this->getProjectRepo();
            $project = $repo->find($pid);
            if ($project != null) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($project);
                $em->flush();

                // Success
                $this->addFlash('success', 'Project successfully deleted!');
            }
        }
        return $this->redirectToRoute('admin_project_all');
    }

    /**
     * @Route("/edit/{pid}", name="admin_project_edit", requirements={"page": "\d+"})
     */
    public function editAction(Request $request, $pid)
    {
        if ($pid == null)
            throw new NotFoundHttpException();

        $repo = $this->getProjectRepo();
        $project = $repo->find($pid);

        if ($project == null)
            throw new NotFoundHttpException();

        $form = $this->createForm(ProjectType::class, $project);
        $form->add('save', SubmitType::class, ['label' => 'Save']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // Show success message and redirect
            $this->addFlash('success', 'Project successfully updated!');
        }

        return $this->render('CustoMoodAppBundle::Admin/Project/new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit project',
            'project' => $project
        ]);
    }

    /**
     * @Route("/settings/edit/{pid}", name="admin_project_settings_edit", requirements={"page": "\d+"})
     */
    public function settingsEdit(Request $request, $pid)
    {
        if ($pid == null)
            throw new NotFoundHttpException();

        $repo = $this->getProjectRepo();
        /** @var Project $project */
        $project = $repo->find($pid);

        if ($project == null)
            throw new NotFoundHttpException();

        // Get settings
        /** @var AdapterService $adapterService */
        $adapterService = $this->get('customood.adapter');
        $adapter = $adapterService->getAdapter($project->getAdapterId());
        if ($adapter == null) {
            $this->addFlash('error', "Can't find project's adapter. Is it installed properly?");
            return $this->redirectToRoute('admin_project_all');

        }
        $settings = ProjectSettingMapper::createProjectSettings($adapter::getSettingsSchema());
        $savedSettings = $project->getSettings();

        // Create form
        $form = $this->createFormBuilder();
        /** @var ProjectSetting $setting */
        foreach ($settings as $setting) {
            switch ($setting->getType()) {
                case SettingType::BOOLEAN:
                    $fieldType = CheckboxType::class;
                    break;
                case SettingType::NUMBER:
                    $fieldType = IntegerType::class;
                    break;
                case SettingType::STRING:
                default:
                    $fieldType = TextType::class;
                    break;
            }

            // Find saved setting value, if any
            $settingValue = null;
            foreach ($savedSettings as $savedSetting) {
                if ($savedSetting->getName() == $setting->getName()) {
                    $settingValue = $savedSetting->getFormattedValue();
                }
            }

            $form->add($setting->getName(), $fieldType, [
                'label' => $setting->getDisplayName() ?: $setting->getName(),
                'required' => $setting->getRequired(),
                'data' => $settingValue ?: $setting->getFormattedValue()
            ]);
        }

        $form
            ->add('save', SubmitType::class, array('label' => 'Save'));
        $form = $form->getForm();

        // Handle request, if posted
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $settingsRepo = $em->getRepository(ProjectSetting::class);

            // Now save settings if any
            if (count($results) > 0) {
                foreach ($settings as $oldSetting) {
                    $setting = $settingsRepo->findOneBy([
                        'name' => $oldSetting->getName(),
                        'project' => $project->getId()
                    ]);
                    if ($setting == null) {
                        $setting = $oldSetting;
                        $setting->setProject($project);
                        $em->persist($setting);
                    }

                    if ($setting != null) {
                        $result = $results[$setting->getName()];
                        $setting->setValue($result);
                    }
                }

                $em->flush();

                // Show success message
                $this->addFlash('success', 'Project setting successfully updated!');
            }
        }

        return $this->render('CustoMoodAppBundle::Admin/Project/settings_new.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit project - settings',
            'project' => $project
        ]);
    }

    /**
     * @Route("/new", name="admin_project_new")
     */
    public function newAction(Request $request)
    {
        // Check if there are any adapters first
        /** @var AdapterService $adapterService */
        $adapterService = $this->get('customood.adapter');
        if (!$adapterService->adaptersAvailable()) {
            $this->addFlash('error', 'There are no adapters installed.');
            return $this->render('CustoMoodAppBundle::Admin/Project/new.html.twig', [
                'disabled' => true
            ]);
        }

        // Create initial form
        $project = new Project();
        $initForm = $this->createForm(ProjectType::class, $project);
        $initForm->add('save', SubmitType::class, ['label' => 'Next']);

        // Handle init form submission
        $initForm->handleRequest($request);
        if ($initForm->isSubmitted() && $initForm->isValid()) {
            // Project entity valid, move to next stage - adapter settings

            // Clear request (simulate a GET request)
            $request->request->remove('project');
            $request->setMethod('GET');

            return $this->forward('CustoMoodAppBundle:Project:settingsNew', [
                'projectName' => $project->getName(),
                'projectDescription' => $project->getDescription(),
                'projectAdapterId' => $project->getAdapterId()
            ]);
        }

        return $this->render('CustoMoodAppBundle::Admin/Project/new.html.twig', [
            'form' => $initForm->createView(),
            'title' => 'New project',
            'new' => true
        ]);
    }

    /**
     * @Route("/settings/new", name="admin_project_settings_new")
     */
    public function settingsNewAction(Request $request)
    {
        $projectName = $request->get('projectName') ?: $request->get('form')['projectName'];
        $projectDescription = $request->get('projectDescription') ?: $request->get('form')['projectDescription'];
        $projectAdapterId = $request->get('projectAdapterId') ?: $request->get('form')['projectAdapterId'];

        if ($projectName == null || $projectAdapterId == null || $projectDescription == null)
            return $this->redirectToRoute('admin_project_new');

        // Get settings
        /** @var AdapterService $adapterService */
        $adapterService = $this->get('customood.adapter');
        $adapter = $adapterService->getAdapter($projectAdapterId);
        if ($adapter == null) {
            return $this->redirectToRoute('admin_project_new');

        }
        $settings = ProjectSettingMapper::createProjectSettings($adapter::getSettingsSchema());

        // Create form
        $form = $this->createFormBuilder();
        /** @var ProjectSetting $setting */
        foreach ($settings as $setting) {
            switch ($setting->getType()) {
                case SettingType::BOOLEAN:
                    $fieldType = CheckboxType::class;
                    break;
                case SettingType::NUMBER:
                    $fieldType = IntegerType::class;
                    break;
                case SettingType::STRING:
                default:
                    $fieldType = TextType::class;
                    break;
            }

            $form->add($setting->getName(), $fieldType, [
                'label' => $setting->getDisplayName() ?: $setting->getName(),
                'required' => $setting->getRequired(),
                'data' => $setting->getFormattedValue()
            ]);
        }

        $form
            ->setAction($this->generateUrl('admin_project_settings_new'))
            ->add('projectName', HiddenType::class, ['data' => $projectName])
            ->add('projectDescription', HiddenType::class, ['data' => $projectDescription])
            ->add('projectAdapterId', HiddenType::class, ['data' => $projectAdapterId])
            ->add('save', SubmitType::class, array('label' => 'Save'));
        $form = $form->getForm();

        // Handle request, if posted
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $settingsRepo = $em->getRepository(ProjectSetting::class);

            // First create project
            $project = new Project();
            $project
                ->setName($projectName)
                ->setDescription($projectDescription)
                ->setAdapterId($projectAdapterId);

            $em->persist($project);
            $em->flush();

            // Now save settings if any
            if (count($results) > 0) {
                foreach ($settings as $oldSetting) {
                    $setting = $settingsRepo->findOneBy(['name' => $oldSetting->getName(), 'project' => $project->getId()]);
                    if ($setting == null) {
                        $setting = $oldSetting;
                        $setting->setProject($project);
                        $em->persist($setting);
                    }

                    if ($setting != null) {
                        $result = $results[$setting->getName()];
                        $setting->setValue($result);
                    }
                }

                $em->flush();

                // Show success message and redirect
                $this->addFlash('success', 'Project successfully created!');
                return $this->redirectToRoute('admin_project_all');
            }
        }

        return $this->render('CustoMoodAppBundle::Admin/Project/settings_new.html.twig', [
            'form' => $form->createView(),
            'title' => 'New project - settings'
        ]);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository|ProjectRepository
     */
    protected function getProjectRepo()
    {
        return $this->getDoctrine()->getRepository(Project::class);
    }
}