<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

use OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;
use Magento\Backend\Model\View\Result\Redirect;
use Exception;

class Delete extends Attribute
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            $model = $this->attributeFactory->create();
            $model->load($id);

            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('We can\'t delete the attribute.'));
                return $resultRedirect->setPath('categoryattribute/*/');
            }

            try {
                $model->delete();
                $this->reindexCategoryFlatData();
                $this->messageManager->addSuccess(__('You deleted the category attribute.'));
                return $resultRedirect->setPath('categoryattribute/*/');
            } catch (Exception $e) {
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
