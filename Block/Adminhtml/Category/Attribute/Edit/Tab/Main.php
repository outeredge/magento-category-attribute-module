<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends AbstractMain
{
    /**
     * Adding category form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();
        /* @var $form \Magento\Framework\Data\Form */
        $form = $this->getForm();
        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        $fieldsToRemove = [
            'attribute_code',
            'is_unique',
            'frontend_class',
            'is_required'
        ];

        foreach ($fieldset->getElements() as $element) {
            /** @var \Magento\Framework\Data\Form\AbstractForm $element  */
            if (substr($element->getId(), 0, strlen('default_value')) == 'default_value') {
                $fieldsToRemove[] = $element->getId();
            }
        }
        foreach ($fieldsToRemove as $id) {
            $fieldset->removeField($id);
        }

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = [
            ['value' => 'price', 'label' => __('Price')],
            ['value' => 'media_image', 'label' => __('Media Image')],
        ];
        $additionalReadOnlyTypes = ['gallery' => __('Gallery')];
        if (isset($additionalReadOnlyTypes[$attributeObject->getFrontendInput()])) {
            $additionalTypes[] = [
                'value' => $attributeObject->getFrontendInput(),
                'label' => $additionalReadOnlyTypes[$attributeObject->getFrontendInput()],
            ];
        }

        $response = new \Magento\Framework\DataObject();
        $response->setTypes([]);
        $this->_eventManager->dispatch('adminhtml_category_attribute_types', ['response' => $response]);
        $_hiddenFields = [];
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
        }
        $this->_coreRegistry->register('attribute_type_hidden_fields', $_hiddenFields);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $this->_eventManager->dispatch('category_attribute_form_build_main_tab', ['form' => $form]);

        return $this;
    }

    /**
     * Retrieve additional element types for category attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return ['apply' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Apply'];
    }
}
