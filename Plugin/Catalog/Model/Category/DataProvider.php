<?php

namespace OuterEdge\CategoryAttribute\Plugin\Catalog\Model\Category;

use Magento\Store\Model\StoreManagerInterface;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Framework\UrlInterface;

class DataProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryAttributeHelper $categoryAttributeHelper
     */
    private $categoryAttributeHelper;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CategoryAttributeHelper $categoryAttributeHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryAttributeHelper $categoryAttributeHelper
    ) {
        $this->storeManager = $storeManager;
        $this->categoryAttributeHelper = $categoryAttributeHelper;
    }

    public function afterGetData(CategoryDataProvider $subject, array $loadedData)
    {
        if (empty($loadedData)) {
            return $loadedData;
        }

        $category = $subject->getCurrentCategory();
        if (!$category) {
            return $loadedData;
        }

        if (empty($this->categoryAttributeHelper->getCustomImageAttributesAsArray())) {
            return $loadedData;
        }

        foreach ($loadedData as $categoryId => $categoryData) {
            foreach ($this->categoryAttributeHelper->getCustomImageAttributesAsArray() as $image) {
                if (isset($categoryData[$image])) {
                    $url = $this->storeManager->getStore()->getBaseUrl(
                        UrlInterface::URL_TYPE_MEDIA
                    ) . 'catalog/category/' . $categoryData[$image];

                    $loadedData[$categoryId][$image] = [[
                        'name' => $categoryData[$image],
                        'url'  => $url
                    ]];
                }
            }
        }

        return $loadedData;
    }
}
