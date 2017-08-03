<?php

namespace OuterEdge\CategoryAttribute\Plugin\Catalog\Model\Product\Media;

class Config
{
    /**
     * @var \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     */
    private $categoryAttributeHelper;
    
    /**
     * @param \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     */
    public function __construct(
        \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
    ) {
        $this->categoryAttributeHelper = $categoryAttributeHelper;
    }
    
    public function afterGetMediaAttributeCodes(\Magento\Catalog\Model\Product\Media\Config $subject, $attributeCodes)
    {
        return array_diff($attributeCodes, $this->categoryAttributeHelper->getCustomImageAttributesAsArray());
    }
}