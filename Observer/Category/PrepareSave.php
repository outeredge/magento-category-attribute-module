<?php

namespace OuterEdge\CategoryAttribute\Observer\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;

class PrepareSave implements ObserverInterface
{
    /**
     * @var CategoryAttributeHelper
     */
    protected $categoryAttributeHelper;

    /**
     * @param CategoryAttributeHelper $categoryAttributeHelper
     */
    public function __construct(
        CategoryAttributeHelper $categoryAttributeHelper
    ) {
        $this->categoryAttributeHelper = $categoryAttributeHelper;
    }

    /**
     * Prepare image data
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (empty($this->categoryAttributeHelper->getCustomImageAttributesAsArray())) {
            return $this;
        }

        $category = $observer->getCategory();
        $data = $observer->getRequest()->getPostValue();

        foreach ($this->categoryAttributeHelper->getCustomImageAttributesAsArray() as $image) {
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
}
