<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model\Service;

class Numbered extends \SalesIds\SocialShare\Model\Service
{
    /**
     * Retrieve collection instance
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        return $collection->addFieldToFilter('can_numbered', true);
    }
}
