<?php

namespace CustoMood\Bundle\AppBundle\Controller;

use CustoMood\Bundle\AppBundle\BaseAdapter\BaseAdapterInterface;
use CustoMood\Bundle\AppBundle\Entity\Project;
use CustoMood\Bundle\AppBundle\Form\MoodParametersType;
use CustoMood\Bundle\AppBundle\Helper\ProjectSettingMapper;
use CustoMood\Bundle\AppBundle\Model\MoodParameters;
use CustoMood\Bundle\AppBUndle\Service\AdapterService;
use CustoMood\Bundle\AppBundle\Service\AnalysisService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/")
 */
class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('CustoMoodAppBundle::Main/index.html.twig');
    }

    /**
     * @Route("/view/{pid}", name="homepage_view_project", requirements={"page": "\d+"})
     */
    public function viewAction(Request $request, $pid)
    {
        // Find project
        if ($pid == null)
            throw new NotFoundHttpException();

        $repo = $this->getDoctrine()->getManager()->getRepository(Project::class);
        $project = $repo->find($pid);

        if ($project == null)
            throw new NotFoundHttpException();

        $moodParams = new MoodParameters();
        $form = $this->createForm(MoodParametersType::class, $moodParams);
        $form
            ->add('submit', SubmitType::class, ['label' => 'Submit']);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get adapter
            /** @var AdapterService $adapterService */
            $adapterService = $this->get('customood.adapter');
            $adapter = $adapterService->getAdapter($project->getAdapterId());
            if ($adapter == null) {
                // Adapter not installed or disabled
                $this->addFlash('error', 'Adapter for this project has been disabled or has been uninstalled.');
                return $this->render('CustoMoodAppBundle::Main/index.html.twig');
            }

            // Get project settings
            $settings = ProjectSettingMapper::createKeyValueArray($project->getSettings());

            // Instantiate adapter and get data
            /** @var BaseAdapterInterface $realAdapter */
            $realAdapter = new $adapter();
            $realAdapter->load($project->getId(), $settings);
            $data = $realAdapter->getMood($moodParams->getMoodFrom(), $moodParams->getMoodTo());

            // Validate data
            /** @var AnalysisService $analysisService */
            $analysisService = $this->get('customood.analysis');
            if ($analysisService->validateReturnedData($data)) {
                // Data is valid, analyze it
                $data = $analysisService->analyzeData($data, $moodParams->getAggregatePeriod());
                $chartData = [
                    'title' => "Client satisfaction for '" . $project->getName() . "'",
                    'data' => [
                        'labels' => $data[0],
                        'values' => $data[1]
                    ]
                ];

                return $this->render('CustoMoodAppBundle::Main/view.html.twig', [
                    'form' => $form->createView(),
                    'project' => $project,
                    'data' => $chartData
                ]);
            } else {
                // Adapter returns invalid data
                $this->addFlash('error', 'Adapter returns invalid data.');
            }
        }

        return $this->render('CustoMoodAppBundle::Main/view.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

}
