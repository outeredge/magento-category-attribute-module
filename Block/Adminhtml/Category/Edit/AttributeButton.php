<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class AttributeButton extends Template implements ButtonProviderInterface
{
    /**
     * Attribute button for category edit page
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'id' => 'attribute',
            'label' => __('Create New Attribute'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('categoryattribute/category_attribute/new')),
            'class' => 'action-secondary',
            'sort_order' => 20
        ];
    }
}
