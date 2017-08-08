<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Image;

use Magento\Catalog\Controller\Adminhtml\Category\Image\Upload as CategoryImageUpload;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Exception;

class Upload extends CategoryImageUpload
{
    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $result = $this->imageUploader->saveFileToTmpDir($attributeCode);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
