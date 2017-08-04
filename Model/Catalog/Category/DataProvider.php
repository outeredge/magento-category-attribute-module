<?php

namespace OuterEdge\CategoryAttribute\Model\Catalog\Category;

use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Eav\Model\Config;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory;

class DataProvider extends CategoryDataProvider
{
    /**
     * @var CategoryAttributeHelper
     */
    protected $categoryAttributeHelper;

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
     * @param CategoryAttributeHelper $categoryAttributeHelper
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
        CategoryAttributeHelper $categoryAttributeHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->categoryAttributeHelper = $categoryAttributeHelper;

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

    /**
     * Convert Yes/No attribute to dropdown on category edit page
     * Convert Image attribute to file uploader on category edit page
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta($meta)
    {
        $meta = parent::prepareMeta($meta);

        foreach ($this->categoryAttributeHelper->getCustomAttributesAsArray() as $attribute) {
            if (isset($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'])) {
                if ($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] === 'boolean') {
                    // Convert boolean attributes to Yes/No select
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] = 'select';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['formElement'] = 'select';
                    $meta['attributes']['children'][$attribute]['arguments']['data']['config']['default'] = 0;
                } elseif ($meta['attributes']['children'][$attribute]['arguments']['data']['config']['dataType'] === 'media_image') {
                    // Convert media_image attributes to image uploader
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

    /**
     * Extend base Magento fields map with custom created attributes
     *
     * @return array
     */
    protected function getFieldsMap()
    {
        return array_merge_recursive(
            parent::getFieldsMap(),
            ['attributes' => $this->categoryAttributeHelper->getCustomAttributesAsArray()]
        );
    }
}
