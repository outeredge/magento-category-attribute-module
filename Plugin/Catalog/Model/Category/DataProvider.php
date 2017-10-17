<?php

namespace OuterEdge\CategoryAttribute\Plugin\Catalog\Model\Category;

use Magento\Store\Model\StoreManagerInterface;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Framework\Filesystem;

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
     * @var Filesystem
     */
    private $fileInfo;

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
    
    /**
     * Set custom image attribute size & type
     * 
     * @param CategoryDataProvider $subject
     * @param array $loadedData
     */
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
                    $fileName = $category->getData($image);
                    if ($this->getFileInfo()->isExist($fileName)) {
                        $stat = $this->getFileInfo()->getStat($fileName);
                        $mime = $this->getFileInfo()->getMimeType($fileName);
                        $loadedData[$categoryId][$image][0]['size'] = isset($stat) ? $stat['size'] : 0;
                        $loadedData[$categoryId][$image][0]['type'] = $mime;
                    }
                }
            }
        }

        return $loadedData;
    }
    
    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
