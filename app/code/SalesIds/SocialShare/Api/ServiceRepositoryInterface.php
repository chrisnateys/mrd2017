<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Api;

use SalesIds\SocialShare\Api\Data\ServiceInterface as DataServiceInterface;

interface ServiceRepositoryInterface
{
    /**
     * Save service
     *
     * @param DataServiceInterface $service
     * @return DataServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DataServiceInterface $service);

    /**
     * Delete service
     *
     * @param DataServiceInterface $service
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DataServiceInterface $service);
}
