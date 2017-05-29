<?php

namespace CustoMood\Bundle\AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class SettingType extends AbstractEnumType
{
    const STRING = 0;
    const NUMBER = 1;
    const BOOLEAN = 2;

    protected static $choices = [
        self::STRING => 'String',
        self::NUMBER => 'Number',
        self::BOOLEAN => 'Boolean'
    ];
}