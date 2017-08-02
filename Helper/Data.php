<?php

namespace OuterEdge\CategoryAttribute\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Model\Context;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
     */
    protected $attributeCollectionFactory;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection
     */
    protected $customAttributes;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection
     */
    protected $customImageAttributes;
    
    /**
     * @var array
     */
    protected $customAttributesArray;
    
    /**
     * @var array
     */
    protected $customImageAttributesArray;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct($context);
    }
    
    public function getCustomAttributes()
    {
        if (!$this->customAttributes) {
            $this->customAttributes = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_user_defined', ['eq' => true]);
        }
        return $this->customAttributes;
    }
    
    public function getCustomImageAttributes()
    {
        if (!$this->customImageAttributes) {
            $this->customImageAttributes = $this->getCustomAttributes()
                ->addFieldToFilter('frontend_input', ['eq' => 'media_image']);
        }
        return $this->customImageAttributes;
    }
    
    public function getCustomAttributesAsArray()
    {
        if (!$this->customAttributesArray) {
            $this->customAttributesArray = [];
            foreach ($this->getCustomAttributes() as $attribute) {
                $this->customAttributesArray[] = $attribute->getAttributeCode();
            }
        }
        return $this->customAttributesArray;
    }
    
    public function getCustomImageAttributesAsArray()
    {
        if (!$this->customImageAttributesArray) {
            $this->customImageAttributesArray = [];
            foreach ($this->getCustomImageAttributes() as $attribute) {
                $this->customImageAttributesArray[] = $attribute->getAttributeCode();
            }
        }
        return $this->customImageAttributesArray;
    }
}
