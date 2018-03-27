<?php

namespace OuterEdge\CategoryAttribute\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection;

class Data extends AbstractHelper
{
    /**
     * @var CollectionFactory $attributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var Collection
     */
    private $customAttributes;

    /**
     * @var Collection
     */
    private $customImageAttributes;

    /**
     * @var array
     */
    private $customAttributesArray;

    /**
     * @var array
     */
    private $customImageAttributesArray;

    /**
     * @param Context $context
     * @param CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return Collection
     */
    public function getCustomAttributes()
    {
        if (!$this->customAttributes) {
            $this->customAttributes = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_user_defined', ['eq' => true]);
        }
        return $this->customAttributes;
    }

    /**
     * @return Collection
     */
    public function getCustomImageAttributes()
    {
        if (!$this->customImageAttributes) {
            $this->customImageAttributes = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_user_defined', ['eq' => true])
                ->addFieldToFilter('frontend_input', ['eq' => 'media_image']);
        }
        return $this->customImageAttributes;
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
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
