<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

use OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

class Delete extends Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('We can\'t delete the attribute.'));
                return $resultRedirect->setPath('categoryattribute/*/');
            }

            try {
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the category attribute.'));
                return $resultRedirect->setPath('categoryattribute/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    'categoryattribute/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addError(__('We can\'t find an attribute to delete.'));
        return $resultRedirect->setPath('categoryattribute/*/');
    }
}
