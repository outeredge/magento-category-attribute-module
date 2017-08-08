<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Advanced as AttributeTabAdvanced;

class Advanced extends AttributeTabAdvanced
{
    /**
     * @var array
     */
    private $fieldsToRemove = [
        'default_value_text',
        'default_value_yesno',
        'default_value_date',
        'default_value_textarea',
        'is_unique',
        'is_used_in_grid',
        'is_visible_in_grid',
        'is_filterable_in_grid',
        'is_global'
    ];

    /**
     * Adding product/category form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $fieldset = $form->getElement('advanced_fieldset');
        foreach ($this->fieldsToRemove as $field) {
            $fieldset->removeField($field);
        }

        $this->_eventManager->dispatch('category_attribute_form_build', ['form' => $form]);

        return $this;
    }
}
