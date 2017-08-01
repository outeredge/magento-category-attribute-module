<?php

namespace OuterEdge\CategoryAttribute\Model\Catalog\Category;

use Magento\Catalog\Model\Category\DataProvider;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;

class DataProviderPlugin
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;
    
    /**
     * @var array
     */
    private $userImageAttributes;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
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
        
        if (empty($this->getUserImageAttributes())) {
            return $loadedData;
        }
        
        foreach ($loadedData as $categoryId => $categoryData) {
            foreach ($this->getUserImageAttributes() as $image) {
                if (isset($categoryData[$image])) {
                    
                    $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
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
    
    protected function getUserImageAttributes()
    {
        if (!$this->userImageAttributes) {
            $attributeCollection = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_user_defined', ['eq' => true])
                ->addFieldToFilter('frontend_input', ['eq' => 'media_image']);
            
            foreach ($attributeCollection as $attribute) {
                $this->userImageAttributes[] = $attribute->getAttributeCode();
            }
        }
        return $this->userImageAttributes;
    }
}