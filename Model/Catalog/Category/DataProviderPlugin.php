<?php

namespace OuterEdge\CategoryAttribute\Model\Catalog\Category;

use Magento\Catalog\Model\Category\DataProvider;
use Magento\Framework\UrlInterface;

class DataProviderPlugin
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     */
    private $categoryAttributeHelper;
    
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
    ) {
        $this->storeManager = $storeManager;
        $this->categoryAttributeHelper = $categoryAttributeHelper;
    }
    
    public function afterGetData(DataProvider $subject, array $loadedData)
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
