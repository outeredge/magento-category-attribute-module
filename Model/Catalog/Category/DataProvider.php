<?php

namespace OuterEdge\CategoryAttribute\Model\Catalog\Category;

use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Eav\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\CategoryFactory;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;

class DataProvider extends CategoryDataProvider
{
    /**
     * @var CategoryAttributeHelper
     */
    private $categoryAttributeHelper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EavValidationRules $eavValidationRules
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Config $eavConfig
     * @param RequestInterface $request
     * @param CategoryFactory $categoryFactory
     * @param CategoryAttributeHelper $categoryAttributeHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Config $eavConfig,
        RequestInterface $request,
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
            if (isset($meta['attributes']['children'][$attribute]['arguments']['data']['config'])) {
                $attributeConfig = &$meta['attributes']['children'][$attribute]['arguments']['data']['config'];
                if (isset($attributeConfig['dataType'])) {
                    if ($attributeConfig['dataType'] === 'boolean') {
                        $attributeConfig = array_merge($attributeConfig, [
                            'dataType'    => 'select',
                            'formElement' => 'select',
                            'default'     => 0
                        ]);
                    } elseif ($attributeConfig['dataType'] === 'media_image') {
                        $attributeConfig = array_merge($attributeConfig, [
                            'dataType'       => 'image',
                            'formElement'    => 'fileUploader',
                            'uploaderConfig' => [
                                'url' => 'categoryattribute/category_image/upload/attribute_code/' . $attribute
                            ]
                        ]);
                    }
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
