<?php

namespace CustoMood\Bundle\AppBundle\Model;

class AdapterWebListingItem extends AdapterItem
{
    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * AdapterWebListingItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
    }


    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return AdapterWebListingItem
     */
    public function setEnabled(bool $enabled): AdapterWebListingItem
    {
        $this->enabled = $enabled;
        return $this;
    }
}