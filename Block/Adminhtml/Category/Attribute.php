<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category;

use Magento\Backend\Block\Widget\Grid\Container;

class Attribute extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category_attribute';
        $this->_blockGroup = 'OuterEdge_CategoryAttribute';
        $this->_headerText = __('Category Attributes');
        $this->_addButtonLabel = __('Create New Attribute');
        parent::_construct();
    }
}
