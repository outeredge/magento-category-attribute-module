<?php

namespace OuterEdge\CategoryAttribute\Plugin\Catalog\Model\Product\Media;

use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;

class Config
{
    /**
     * @var CategoryAttributeHelper $categoryAttributeHelper
     */
    private $categoryAttributeHelper;

    /**
     * @param CategoryAttributeHelper $categoryAttributeHelper
     */
    public function __construct(
        CategoryAttributeHelper $categoryAttributeHelper
    ) {
        $this->categoryAttributeHelper = $categoryAttributeHelper;
    }

    /**
     * @param MediaConfig $subject
     * @param array $attributeCodes
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMediaAttributeCodes(MediaConfig $subject, $attributeCodes)
    {
        return array_diff($attributeCodes, $this->categoryAttributeHelper->getCustomImageAttributesAsArray());
    }
}
