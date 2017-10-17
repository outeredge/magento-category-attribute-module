<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\EntityFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Phrase;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Catalog\Model\Indexer\Category\Flat\State;

abstract class Attribute extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'OuterEdge_CategoryAttribute::attributes';

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;
    
    /**
     * @var State
     */
    protected $flatState;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param EntityFactory $entityFactory
     * @param IndexerRegistry $indexerRegistry
     * @param State $flatState
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        AttributeFactory $attributeFactory,
        EntityFactory $entityFactory,
        IndexerRegistry $indexerRegistry,
        State $flatState
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->attributeFactory = $attributeFactory;
        $this->entityFactory = $entityFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->flatState = $flatState;
        parent::__construct($context);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_entityTypeId = $this->entityFactory->create()
            ->setType(Category::ENTITY)
            ->getTypeId();
        return parent::dispatch($request);
    }

    /**
     * @param Phrase|null $title
     * @return Page
     */
    protected function createActionPage($title = null)
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Catalog'), __('Catalog'))
            ->addBreadcrumb(__('Manage Category Attributes'), __('Manage Category Attributes'))
            ->setActiveMenu('OuterEdge_CategoryAttribute::attributes');
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->getConfig()->getTitle()->prepend(__('Category Attributes'));
        return $resultPage;
    }

    /**
     * Reindex the category flat data
     * Needed when adding/deleting attributes
     *
     * @return void
     */
    protected function reindexCategoryFlatData()
    {
        if ($this->flatState->isFlatEnabled()) {
            $flatIndexer = $this->indexerRegistry->get(State::INDEXER_ID);
            if (!$flatIndexer->isScheduled()) {
                $flatIndexer->reindexAll();
            }
        }
    }
}
