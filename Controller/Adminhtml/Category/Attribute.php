<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result;
use Magento\Framework\View\Result\PageFactory;

abstract class Attribute extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'OuterEdge_CategoryAttribute::attributes';

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $_attributeLabelCache;

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
     */
    protected $indexerFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
     *
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_attributeLabelCache = $attributeLabelCache;
        $this->resultPageFactory = $resultPageFactory;
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }
    */
   
   /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }
    

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Catalog\Model\Category::ENTITY
        )->getTypeId();
        return parent::dispatch($request);
    }

    /**
     * @param \Magento\Framework\Phrase|null $title
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($this->getRequest()->getParam('popup')) {
            if ($this->getRequest()->getParam('product_tab') === 'variations') {
                $resultPage->addHandle(['popup', 'catalog_product_attribute_edit_product_tab_variations_popup']);
            } else {
                $resultPage->addHandle(['popup', 'catalog_product_attribute_edit_popup']);
            }
            $pageConfig = $resultPage->getConfig();
            $pageConfig->addBodyClass('attribute-popup');
        } else {
            $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'))
                ->addBreadcrumb(__('Manage Category Attributes'), __('Manage Category Attributes'))
                ->setActiveMenu('OuterEdge_CategoryAttribute::attributes');
            if (!empty($title)) {
                $resultPage->addBreadcrumb($title, $title);
            }
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Category Attributes'));
        return $resultPage;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }
    
    /**
     * Reindex the category flat data
     * Needed when adding/deleting attributes
     * 
     * @return null
     */
    protected function reindexCategoryFlatData()
    {
        $this->indexerFactory->create()
            ->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID)
            ->reindexAll();
    }
}
