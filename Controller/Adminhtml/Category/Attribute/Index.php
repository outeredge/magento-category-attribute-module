<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

use OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

class Index extends Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute')
        );
        return $resultPage;
    }
}
