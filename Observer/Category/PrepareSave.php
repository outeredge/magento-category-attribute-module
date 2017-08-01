<?php

namespace OuterEdge\CategoryAttribute\Observer\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;

class PrepareSave implements ObserverInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;
    
    /**
     * @var array
     */
    private $userImageAttributes;
    
    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }
    
    /**
     * Prepare image data
     * 
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (empty($this->getUserImageAttributes())) {
            return $this;
        }
        
        $category = $observer->getCategory();
        $data = $observer->getRequest()->getPostValue();
        
        foreach ($this->getUserImageAttributes() as $image) {
            if (empty($data[$image])) {
                $category->setData($image, null);
            } else {
                if (isset($data[$image]) && is_array($data[$image])) {
                    if (!empty($data[$image]['delete'])) {
                        $data[$image] = null;
                    } else {
                        if (isset($data[$image][0]['name']) && isset($data[$image][0]['tmp_name'])) {
                            $data[$image] = $data[$image][0]['name'];
                        } else {
                            unset($data[$image]);
                        }
                    }
                }
                if (isset($data[$image])) {
                    $category->setData($image, $data[$image]);
                }
            }
        }
        
        return $this;
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