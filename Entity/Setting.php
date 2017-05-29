<?php

namespace CustoMood\Bundle\AppBundle\Entity;

use CustoMood\Bundle\AppBundle\DBAL\Types\SettingType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="CustoMood\Bundle\AppBundle\Repository\SettingRepository")
 */
class Setting extends SettingBase
{
}