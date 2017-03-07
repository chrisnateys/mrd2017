<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model\ResourceModel\Service;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('SalesIds\SocialShare\Model\Service', 'SalesIds\SocialShare\Model\ResourceModel\Service');
    }

    /**
     * Order collection by ids
     *
     * @param string|array $serviceIds
     * @return SalesIds\SocialShare\Model\ResourceModel\Service\Collection
     */
    public function orderByIds($serviceIds)
    {
        if (!$serviceIds || empty($serviceIds)) {
            return $this;
        }

        if (is_array($serviceIds)) {
            $serviceIds = implode(',', $serviceIds);
        }
        // Sorting the collection
        $condition = sprintf('FIELD(service_id, %s)', $serviceIds);
        $this->getSelect()->order(
            new \Zend_Db_Expr($condition)
        );
        return $this;
    }
}
