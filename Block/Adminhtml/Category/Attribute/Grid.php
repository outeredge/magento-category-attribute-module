<?php

namespace OuterEdge\CategoryAttribute\Block\Adminhtml\Category\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use OuterEdge\CategoryAttribute\Helper\Data as CategoryAttributeHelper;

class Grid extends AbstractGrid
{
    /**
     * @var CategoryAttributeHelper $categoryAttributeHelper
     */
    private $categoryAttributeHelper;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param CategoryAttributeHelper $categoryAttributeHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CategoryAttributeHelper $categoryAttributeHelper,
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
        $this->sortColumnsByOrder();

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
