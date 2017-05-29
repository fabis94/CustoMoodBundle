<?php

namespace CustoMood\Bundle\AppBundle\Helper;

use CustoMood\Bundle\AppBundle\BaseAdapter\BaseAdapterInterface;
use CustoMood\Bundle\AppBundle\Model\AdapterWebListingItem;

class AdapterMapper
{
    /**
     * Map BaseAdapterInterface implementation to AdapterWebListingItem
     * @param $adapter BaseAdapterInterface
     * @return AdapterWebListingItem
     */
    public static function ToAdapterWebListingItem($adapter)
    {
        $result = new AdapterWebListingItem();
        $result
            ->setId($adapter::getId())
            ->setWebsite($adapter::getWebsite())
            ->setVersion($adapter::getVersion())
            ->setDescription($adapter::getDescription())
            ->setAuthor($adapter::getAuthor())
            ->setDisplayName($adapter::getDisplayName());

        return $result;
    }

}