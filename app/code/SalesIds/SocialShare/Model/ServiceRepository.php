<?php
/**
 * Copyright © 2016 SalesIds. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SalesIds\SocialShare\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SalesIds\SocialShare\Api\Data\ServiceInterface as DataServiceInterface;
use SalesIds\SocialShare\Api\ServiceRepositoryInterface;
use SalesIds\SocialShare\Model\ResourceModel\Service as ResourceService;
use SalesIds\SocialShare\Model\ServiceFactory;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * @var ResourceService
     */
    protected $_resource;

    /**
     * @var ServiceFactory
     */
    protected $_serviceFactory;

    /**
     * Initialize dependencies
     *
     * @param ResourceService $resource
     */
    public function __construct(
        ResourceService $resource,
        ServiceFactory $serviceFactory
    ) {
        $this->_resource = $resource;
        $this->_serviceFactory = $serviceFactory;
    }

    /**
     * Load service data by given service identity
     *
     * @param int $serviceId
     * @return Service
     * @throws NoSuchEntityException
     */
    public function getById($serviceId)
    {
        $service = $this->_serviceFactory->create();
        $this->_resource->load($service, $serviceId);
        if (!$service->getId()) {
            throw new NoSuchEntityException(__('Service with id "%1" does not exist.', $serviceId));
        }
        return $service;
    }

    /**
     * Save log
     *
     * @param DataServiceInterface $service
     * @return Service
     * @throws CouldNotSaveException
     */
    public function save(DataServiceInterface $service)
    {
        try {
            $this->_resource->save($service);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $service;
    }

    /**
     * Delete log
     *
     * @param DataServiceInterface $service
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DataServiceInterface $service)
    {
        try {
            $this->_resource->delete($service);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}
