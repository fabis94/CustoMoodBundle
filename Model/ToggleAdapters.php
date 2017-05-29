<?php


namespace CustoMood\Bundle\AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Enable/Disable Adapters Form ViewModel
 *
 * Class ToggleAdapters
 * @package CustoMood\Bundle\AppBundle\Model
 */
class ToggleAdapters
{
    /**
     * @var ArrayCollection
     */
    protected $adapters;

    /**
     * ToggleAdapters constructor.
     */
    public function __construct()
    {
        $this->adapters = new ArrayCollection();
    }

    /**
     * @param AdapterWebListingItem $adapter
     * @return $this
     */
    public function addAdapter(AdapterWebListingItem $adapter)
    {
        $this->adapters->add($adapter);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAdapters(): ArrayCollection
    {
        return $this->adapters;
    }


}