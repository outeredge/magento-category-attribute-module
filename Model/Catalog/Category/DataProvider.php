<?php

namespace OuterEdge\CategoryAttribute\Model\Catalog\Category;

use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Eav\Model\Config;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory as AttributeCollectionFactory;

class DataProvider extends CategoryDataProvider
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;
    
    /**
     * @var array
     */
    private $userAttributes;
    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EavValidationRules $eavValidationRules
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param Config $eavConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CategoryFactory $categoryFactory
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Config $eavConfig,
        \Magento\Framework\App\RequestInterface $request,
        CategoryFactory $categoryFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $eavValidationRules,
            $categoryCollectionFactory,
            $storeManager,
            $registry,
            $eavConfig,
            $request,
            $categoryFactory,
            $meta,
            $data
        );
    }
    
    public function prepareMeta($meta)
    {
        $meta = parent::prepareMeta($meta);
        
        // Convert boolean attributes to Yes/No select
        foreach ($this->getUserAttributes() as $attribute) {
            if (isset($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'])) {
                if ($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] === 'boolean') {
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] = 'select';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['formElement'] = 'select';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['default'] = 0;
                } 
                else if ($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] === 'media_image') {
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] = 'image';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['formElement'] = 'fileUploader';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['uploaderConfig'] = [
                        'url' => 'categoryattribute/category_image/upload/attribute_code/' . $attribute
                    ];
                }
            }
        }
        
        return $meta;
    }
    
    protected function getFieldsMap()
    {
        return array_merge_recursive(
            parent::getFieldsMap(),
            ['attributes' => $this->getUserAttributes()]
        );
    }
    
    protected function getUserAttributes()
    {
        if (!$this->userAttributes) {
            $attributeCollection = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('is_user_defined', ['eq' => true]);
            
            foreach ($attributeCollection as $attribute) {
                $this->userAttributes[] = $attribute->getAttributeCode();
            }
        }
        return $this->userAttributes;
    }
}