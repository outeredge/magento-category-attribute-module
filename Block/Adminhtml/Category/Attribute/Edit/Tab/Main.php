<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Main as AttributeTabMain;

class Main extends AttributeTabMain
{
    /**
     * @var array
     */
    private $fieldsToRemove = [
        'update_product_preview_image',
        'use_product_image_for_swatch'
    ];

    /**
     * Adding product/category form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        foreach ($this->fieldsToRemove as $field) {
            $fieldset->removeField($field);
        }

        $this->_eventManager->dispatch('category_attribute_form_build_main_tab', ['form' => $form]);

        return $this;
    }
}
