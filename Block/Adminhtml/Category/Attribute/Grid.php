<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends AbstractGrid
{
    /**
     * @var \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     */
    protected $categoryAttributeHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \OuterEdge\CategoryAttribute\Helper\Data $categoryAttributeHelper,
        array $data = []
    ) {
        $this->_module = 'categoryattribute';
        $this->categoryAttributeHelper = $categoryAttributeHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare category attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->categoryAttributeHelper->getCustomAttributes();
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
        GridExtended::_prepareColumns();
        
        $this->addColumn(
            'attribute_code',
            [
                'header' => __('Attribute Code'),
                'sortable' => true,
                'index' => 'attribute_code',
                'header_css_class' => 'col-attr-code',
                'column_css_class' => 'col-attr-code'
            ]
        );

        $this->addColumn(
            'frontend_label',
            [
                'header' => __('Default Label'),
                'sortable' => true,
                'index' => 'frontend_label',
                'header_css_class' => 'col-label',
                'column_css_class' => 'col-label'
            ]
        );
        
        $this->_eventManager->dispatch('category_attribute_grid_build', ['grid' => $this]);

        return $this;
    }
}
