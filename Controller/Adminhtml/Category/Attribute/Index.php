<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

use OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;
use Magento\Backend\Model\View\Result\Page;

class Index extends Attribute
{
    /**
     * @return Page
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
