<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit;

use Magento\Backend\Block\Widget\Tabs as MagentoTabs;

class Tabs extends MagentoTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            [
                'label' => __('Properties'),
                'title' => __('Properties'),
                'content' => $this->getChildHtml('main'),
                'active' => true
            ]
        );
        $this->addTab(
            'labels',
            [
                'label' => __('Manage Labels'),
                'title' => __('Manage Labels'),
                'content' => $this->getChildHtml('labels')
            ]
        );
        $this->addTab(
            'front',
            [
                'label' => __('Storefront Properties'),
                'title' => __('Storefront Properties'),
                'content' => $this->getChildHtml('front')
            ]
        );

        return parent::_beforeToHtml();
    }
}
