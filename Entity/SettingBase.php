<?php

namespace CustoMood\Bundle\AppBundle\Entity;

use CustoMood\Bundle\AppBundle\DBAL\Types\SettingType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\MappedSuperclass
 */
class SettingBase
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     */
    protected $displayName;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="SettingType", nullable=false)
     * @DoctrineAssert\Enum(entity="CustoMood\Bundle\AppBundle\DBAL\Types\SettingType")
     */
    protected $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=false, options={"default": false})
     */
    protected $required;

    /**
     * @var int
     * @ORM\Column(name="setting_order", type="integer", nullable=false, options={"default": 0})
     */
    protected $settingOrder;

    /**
     * Setting constructor.
     */
    public function __construct()
    {
        $this->required = false;
        $this->type = SettingType::STRING;
        $this->settingOrder = 0;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Setting
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
     * Set value
     *
     * @param mixed $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        switch ($this->getType()) {
            case SettingType::BOOLEAN:
                $this->value = $value ? '1' : '0';
                break;
            default:
                $this->value = (string)$value;
                break;
        }

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get value formatted according to setting type
     * @return int|mixed|string
     */
    public function getFormattedValue()
    {
        switch ($this->getType()) {
            case SettingType::BOOLEAN:
                $value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
                break;
            case SettingType::NUMBER:
                $value = intval($this->value);
                break;
            case SettingType::STRING:
                $value = (string)$this->value;
                break;
            default:
                $value = $this->value;
                break;
        }

        return $value;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return Setting
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Setting
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return Setting
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set order
     *
     * @param integer $settingOrder
     *
     * @return Setting
     */
    public function setSettingOrder($settingOrder)
    {
        $this->settingOrder = $settingOrder;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getSettingOrder()
    {
        return $this->settingOrder;
    }
}
