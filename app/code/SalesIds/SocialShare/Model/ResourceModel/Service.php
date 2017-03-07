<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Service extends AbstractDb
{
    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('salesids_socialshare_service', 'service_id');
    }
}
