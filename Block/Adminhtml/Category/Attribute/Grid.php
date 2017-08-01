<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends AbstractGrid
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_module = 'categoryattribute';
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare category attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('is_user_defined', ['eq' => true]);
            
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare category attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->_eventManager->dispatch('category_attribute_grid_build', ['grid' => $this]);

        return $this;
    }
}
