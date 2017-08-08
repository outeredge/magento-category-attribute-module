<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit as ProductAttributeEdit;
use Magento\Framework\Phrase;

class Edit extends ProductAttributeEdit
{
    /**
     * Retrieve header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('entity_attribute')->getId()) {
            $frontendLabel = $this->_coreRegistry->registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return __('Edit Category Attribute "%1"', $this->escapeHtml($frontendLabel));
        }
        return __('New Category Attribute');
    }

    /**
     * Retrieve URL for validation
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('catalog/product_attribute/validate', ['_current' => true]);
    }

    /**
     * Retrieve URL for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            'categoryattribute/category_attribute/save',
            ['_current' => true, 'back' => null]
        );
    }
}
