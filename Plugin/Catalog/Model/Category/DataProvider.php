<?php
namespace OuterEdge\CategoryAttribute\Plugin\Catalog\Model\Category;

use Magento\Store\Model\StoreManagerInterface;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;
use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class DataProvider
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = '/catalog/category';
    
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
    private $filesystem;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;
    
    /**
     * @param StoreManagerInterface $storeManager
     * @param CategoryAttributeHelper $categoryAttributeHelper
     * @param Filesystem $filesystem
     * @param Mime $mime
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryAttributeHelper $categoryAttributeHelper,
        Filesystem $filesystem,
        Mime $mime
    ) {
        $this->storeManager = $storeManager;
        $this->categoryAttributeHelper = $categoryAttributeHelper;
        $this->filesystem = $filesystem;
        $this->mime = $mime;
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
                    if ($this->isExist($fileName)) {
                        $stat = $this->getStat($fileName);
                        $mime = $this->getMimeType($fileName);
                        $loadedData[$categoryId][$image][0]['size'] = isset($stat) ? $stat['size'] : 0;
                        $loadedData[$categoryId][$image][0]['type'] = $mime;
                    }
                }
            }
        }
        return $loadedData;
    }
    
     /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');

        $result = $this->getMediaDirectory()->stat($filePath);
        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        $filePath = self::ENTITY_MEDIA_PATH . '/' . ltrim($fileName, '/');

        $result = $this->getMediaDirectory()->isExist($filePath);
        return $result;
    }
}
