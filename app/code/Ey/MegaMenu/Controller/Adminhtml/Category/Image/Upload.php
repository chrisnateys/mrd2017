<?php

namespace Ey\MegaMenu\Controller\Adminhtml\Category\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 * @package Ey\MegaMenu\Controller\Adminhtml\Category\Image
 */
class Upload extends \Magento\Catalog\Controller\Adminhtml\Category\Image\Upload
{
    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $fileId = 'image';
            if(isset($_FILES) && is_array($_FILES) && count($_FILES) == 1){
                $fileId = array_keys($_FILES)[0];
            }
            $result = $this->imageUploader->saveFileToTmpDir($fileId);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}