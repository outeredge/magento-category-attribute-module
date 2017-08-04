<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Indexer\IndexerInterfaceFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Phrase;
use Magento\Backend\Model\View\Result\Page;
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
     * @var IndexerInterfaceFactory $indexerFactory
     */
    protected $indexerFactory;

   /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param IndexerInterfaceFactory $indexerFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        IndexerInterfaceFactory $indexerFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }


    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Catalog\Model\Category::ENTITY
        )->getTypeId();
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
            ->load(State::INDEXER_ID)
            ->reindexAll();
    }
}
