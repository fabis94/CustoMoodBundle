<?php


namespace CustoMood\Bundle\AppBundle\DataFixtures\ORM;


use CustoMood\Bundle\AppBundle\DBAL\Types\SettingType;
use CustoMood\Bundle\AppBundle\Entity\Setting;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSettingData implements FixtureInterface
{

    /**
     * Available settings
     */
    const APP_SETTINGS = [
        'registration_open' => [
            'type' => SettingType::BOOLEAN ,
            'displayName' => 'Registration open',
            'value' => 'false',
            'order' => 1
        ],
        'welcome_message' => [
            'type' => SettingType::STRING,
            'displayName' => 'User welcome message',
            'value' => null,
            'order' => 3
        ],
        'maximum_user_amount' => [
            'type' => SettingType::NUMBER,
            'displayName' => 'Max allowed user registrations',
            'value' => 100,
            'required' => true,
            'order' => 2
        ]
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::APP_SETTINGS as $key => $settingData) {
            $setting = new Setting();

            $setting
                ->setName($key)
                ->setValue($settingData['value'])
                ->setType($settingData['type'])
                ->setDisplayName($settingData['displayName'])
                ->setSettingOrder($settingData['order']);

            if (array_key_exists('required', $settingData)) {
                $setting->setRequired($settingData['required']);
            }

            $manager->persist($setting);
        }

        $manager->flush();
    }
}