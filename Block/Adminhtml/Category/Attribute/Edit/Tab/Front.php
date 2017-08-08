<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front as AttributeTabFront;

class Front extends AttributeTabFront
{
    /**
     * @var array
     */
    private $fieldsToRemove = [
        'is_filterable',
        'is_filterable_in_search',
        'is_searchable',
        'is_visible_in_advanced_search',
        'is_comparable',
        'is_used_for_promo_rules',
        'used_in_product_listing',
        'used_for_sort_by',
        'search_weight',
        'position'
    ];

    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();

        $this->_eventManager->dispatch('category_attribute_form_build_front_tab', ['form' => $form]);

        $fieldset = $form->getElement('front_fieldset');
        foreach ($this->fieldsToRemove as $field) {
            $fieldset->removeField($field);
        }

        $attributeObject = $this->_coreRegistry->registry('entity_attribute');

        $this->_eventManager->dispatch(
            'adminhtml_categoryattribute_category_attribute_edit_frontend_prepare_form',
            ['form' => $form, 'attribute' => $attributeObject]
        );

        return $this;
    }
}
