<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Front extends Generic
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param PropertyLocker $propertyLocker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        PropertyLocker $propertyLocker,
        array $data = []
    ) {
        $this->_yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $yesnoSource = $this->_yesNo->toOptionArray();

        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Storefront Properties'), 'collapsable' => $this->getRequest()->has('popup')]
        );

        $this->_eventManager->dispatch('category_attribute_form_build_front_tab', ['form' => $form]);

        $fieldset->addField(
            'is_wysiwyg_enabled',
            'select',
            [
                'name' => 'is_wysiwyg_enabled',
                'label' => __('Enable WYSIWYG'),
                'title' => __('Enable WYSIWYG'),
                'values' => $yesnoSource,
            ]
        );

        $fieldset->addField(
            'is_html_allowed_on_front',
            'select',
            [
                'name' => 'is_html_allowed_on_front',
                'label' => __('Allow HTML Tags on Storefront'),
                'title' => __('Allow HTML Tags on Storefront'),
                'values' => $yesnoSource,
            ]
        );
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField(
            'is_visible_on_front',
            'select',
            [
                'name' => 'is_visible_on_front',
                'label' => __('Visible on Catalog Pages on Storefront'),
                'title' => __('Visible on Catalog Pages on Storefront'),
                'values' => $yesnoSource
            ]
        );

        $this->_eventManager->dispatch(
            'adminhtml_categoryattribute_category_attribute_edit_frontend_prepare_form',
            ['form' => $form, 'attribute' => $attributeObject]
        );

        // define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "is_wysiwyg_enabled",
                'wysiwyg_enabled'
            )->addFieldMap(
                "is_html_allowed_on_front",
                'html_allowed_on_front'
            )->addFieldMap(
                "frontend_input",
                'frontend_input_type'
            )->addFieldDependence(
                'wysiwyg_enabled',
                'frontend_input_type',
                'textarea'
            )->addFieldDependence(
                'html_allowed_on_front',
                'wysiwyg_enabled',
                '0'
            )
        );

        $this->setForm($form);
        $form->setValues($attributeObject->getData());
        $this->propertyLocker->lock($form);
        return parent::_prepareForm();
    }
}
