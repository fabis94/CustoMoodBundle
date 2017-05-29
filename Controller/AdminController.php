<?php

namespace CustoMood\Bundle\AppBundle\Controller;

use CustoMood\Bundle\AppBundle\Helper\AdapterMapper;
use CustoMood\Bundle\AppBundle\Service\AdapterService;
use CustoMood\Bundle\AppBundle\DBAL\Types\SettingType;
use CustoMood\Bundle\AppBundle\Entity\AdapterMetadata;
use CustoMood\Bundle\AppBundle\Entity\Setting;
use CustoMood\Bundle\AppBundle\Form\ClearAllAdapterMetadataType;
use CustoMood\Bundle\AppBundle\Form\ToggleAdaptersType;
use CustoMood\Bundle\AppBundle\Model\AdapterWebListingItem;
use CustoMood\Bundle\AppBundle\Model\ToggleAdapters;
use CustoMood\Bundle\AppBundle\Repository\AdapterMetadataRepository;
use CustoMood\Bundle\AppBundle\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        $clearAllMetadataForm = $this->createForm(ClearAllAdapterMetadataType::class);
        $clearAllMetadataForm->handleRequest($request);

        // Clear all metadata
        if ($clearAllMetadataForm->isSubmitted() && $clearAllMetadataForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $adapterRepo = $this->getAdapterRepo();
            $adapters = $adapterRepo->findAll();
            foreach ($adapters as $adapter) {
                $em->remove($adapter);
            }

            $em->flush();

            // Show success
            $this->addFlash(
                'success',
                'All metadata successfully cleared!'
            );
        }

        return $this->render('CustoMoodAppBundle::Admin/index.html.twig', [
            'forms' => [
                'clear_all_metadata' => $clearAllMetadataForm->createView()
            ]
        ]);
    }

    /**
     * @Route("/settings", name="admin_settings")
     */
    public function settingsAction(Request $request)
    {
        // Dynamically create form according to available settings in DB

        $em = $this->getDoctrine()->getManager();
        /** @var SettingRepository $settingsRepo */
        $settingsRepo = $em->getRepository(Setting::class);
        $settings = $settingsRepo->findAllOrdered();
        $form = $this->createFormBuilder();

        /** @var Setting $setting */
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

        $form->add('save_settings', SubmitType::class, array('label' => 'Save settings'));
        $form = $form->getForm();

        // Handle request, if posted
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $form->getData();

            if (count($results) > 0) {
                foreach ($results as $key => $value) {
                    $setting = $settingsRepo->find($key);
                    if ($setting != null) {
                        $setting->setValue($value);
                    }
                }

                $em->flush();
            }
        }

        return $this->render('CustoMoodAppBundle::Admin/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/adapters", name="admin_adapters")
     */
    public function adaptersAction(Request $request)
    {
        $adapterRepo = $this->getAdapterRepo();

        /** @var AdapterService $adapterService */
        $adapterService = $this->get('customood.adapter');
        $adapters = $adapterService->getAdapters(true);
        $adapterArray = [];

        // Find adapters and add them to array
        foreach ($adapters as $adapter) {
            $adapterItem = AdapterMapper::ToAdapterWebListingItem($adapter);
            $metadata = $adapterRepo->find($adapterItem->getId());
            if ($metadata != null) {
                $adapterItem->setEnabled($metadata->getEnabled());
            }
            $adapterArray[$adapterItem->getId()] = $adapterItem;
        }

        // Add found adapters to form model
        $toggleAdapters = new ToggleAdapters();
        foreach ($adapterArray as $testAdapter) {
            $toggleAdapters->addAdapter($testAdapter);
        }

        // Create and handle form
        $form = $this->createForm(ToggleAdaptersType::class, $toggleAdapters);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Store adapter info in DB
            $em = $this->getDoctrine()->getManager();

            /** @var AdapterWebListingItem $formAdapter */
            foreach ($toggleAdapters->getAdapters() as $formAdapter) {
                $adapter = $adapterRepo->find($formAdapter->getId());

                if ($adapter == null) {
                    $adapter = new AdapterMetadata();
                    $adapter->setId($formAdapter->getId());
                    $em->persist($adapter);
                }

                $adapter->setEnabled($formAdapter->getEnabled());
                $em->flush();
            }
        }

        return $this->render('CustoMoodAppBundle::Admin/adapters.html.twig', [
            'adapters' => $adapterArray,
            'form' => $form->createView()
        ]);
    }

    /**
     * @return AdapterMetadataRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getAdapterRepo()
    {
        return $this->getDoctrine()->getManager()->getRepository(AdapterMetadata::class);
    }
}