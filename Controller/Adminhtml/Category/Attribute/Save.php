<?php

namespace OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;

use OuterEdge\CategoryAttribute\Controller\Adminhtml\Category\Attribute;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\EntityFactory;
use Magento\Framework\Indexer\IndexerInterfaceFactory;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Catalog\Model\Product\UrlFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Zend_Validate_Regex;
use Exception;

/**
 * @SuppressWarnings(PHPMD)
 */
class Save extends Attribute
{
    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param AttributeFactory $attributeFactory
     * @param EntityFactory $entityFactory
     * @param IndexerInterfaceFactory $indexerFactory
     * @param ProductHelper $productHelper
     * @param ValidatorFactory $validatorFactory
     * @param UrlFactory $urlFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        AttributeFactory $attributeFactory,
        EntityFactory $entityFactory,
        IndexerInterfaceFactory $indexerFactory,
        ProductHelper $productHelper,
        ValidatorFactory $validatorFactory,
        UrlFactory $urlFactory,
        ResourceConnection $resource
    ) {
        $this->productHelper = $productHelper;
        $this->validatorFactory = $validatorFactory;
        $this->urlFactory = $urlFactory;
        $this->resource = $resource;
        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $attributeFactory,
            $entityFactory,
            $indexerFactory
        );
    }

    /**
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $attributeId = $this->getRequest()->getParam('attribute_id');
            $attributeCode = $this->getRequest()->getParam('attribute_code')
                ?: $this->generateCode($this->getRequest()->getParam('frontend_label')[0]);
            if (strlen($attributeCode) > 0) {
                $validatorAttrCode = new Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addError(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );
                    return $this->returnResult(
                        'categoryattribute/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true],
                        ['error' => true]
                    );
                }
            }
            $data['attribute_code'] = $attributeCode;

            if (isset($data['frontend_input'])) {
                $inputType = $this->validatorFactory->create();
                if (!$inputType->isValid($data['frontend_input'])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addError($message);
                    }
                    return $this->returnResult(
                        'categoryattribute/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true],
                        ['error' => true]
                    );
                }
            }

            $model = $this->attributeFactory->create();

            if ($attributeId) {
                $model->load($attributeId);

                if (!$model->getId()) {
                    $this->messageManager->addError(__('This attribute no longer exists.'));
                    return $this->returnResult('categoryattribute/*/', [], ['error' => true]);
                }

                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $this->messageManager->addError(__('We can\'t update the attribute.'));
                    $this->_session->setAttributeData($data);
                    return $this->returnResult('categoryattribute/*/', [], ['error' => true]);
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType($data['frontend_input']);
            }

            $data += ['is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => []];

            if ($model->getIsUserDefined() === null || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!$model->getIsUserDefined() && $model->getId()) {
                unset($data['apply_to']);
            }

            $model->addData($data);

            if (!$attributeId) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);

                if ($model->getFrontendInput() === 'media_image') {
                    $model->setBackendModel('Magento\Catalog\Model\Category\Attribute\Backend\Image');
                }
            }

            try {
                $model->save();

                if (!$attributeId) {
                    $this->addAttributeToGroup($model->getAttributeId());
                    $this->reindexCategoryFlatData();
                }

                $this->messageManager->addSuccess(__('You saved the category attribute.'));

                $this->_session->setAttributeData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->returnResult(
                        'categoryattribute/*/edit',
                        ['attribute_id' => $model->getId(), '_current' => true],
                        ['error' => false]
                    );
                }
                return $this->returnResult('categoryattribute/*/', [], ['error' => false]);
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setAttributeData($data);
                return $this->returnResult(
                    'categoryattribute/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true],
                    ['error' => true]
                );
            }
        }
        return $this->returnResult('categoryattribute/*/', [], ['error' => true]);
    }

    /**
     * @param string $path
     * @param array $params
     * @param array $response
     * @return Json|Redirect
     */
    private function returnResult($path = '', array $params = [], array $response = [])
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();
            $layout->initMessages();
            $response['messages'] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response['params'] = $params;
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($response);
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
    }
    /**
     * Define whether request is Ajax
     *
     * @return boolean
     */
    private function isAjax()
    {
        return $this->getRequest()->getParam('isAjax');
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
                $this->urlFactory->create()->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }

    private function addAttributeToGroup($attributeId, $sortOrder = null)
    {
        $setId = 3;
        $groupId = 4;

        $data = [
            'entity_type_id' => $this->_entityTypeId,
            'attribute_set_id' => $setId,
            'attribute_group_id' => $groupId,
            'attribute_id' => $attributeId,
        ];

        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $bind = ['entity_type_id' => $this->_entityTypeId, 'attribute_set_id' => $setId, 'attribute_id' => $attributeId];
        $select = $connection->select()->from(
            $this->resource->getTableName('eav_entity_attribute')
        )->where(
            'entity_type_id = :entity_type_id'
        )->where(
            'attribute_set_id = :attribute_set_id'
        )->where(
            'attribute_id = :attribute_id'
        );
        $row = $connection->fetchRow($select, $bind);
        if ($row) {
            if ($sortOrder !== null) {
                $data['sort_order'] = $sortOrder;
            }

            $connection->update(
                $this->resource->getTableName('eav_entity_attribute'),
                $data,
                $connection->quoteInto('entity_attribute_id=?', $row['entity_attribute_id'])
            );
        } else {
            if ($sortOrder === null) {
                $select = $connection->select()->from(
                    $this->resource->getTableName('eav_entity_attribute'),
                    'MAX(sort_order)'
                )->where(
                    'entity_type_id = :entity_type_id'
                )->where(
                    'attribute_set_id = :attribute_set_id'
                )->where(
                    'attribute_id = :attribute_id'
                );

                $sortOrder = $connection->fetchOne($select, $bind) + 10;
            }
            $sortOrder = is_numeric($sortOrder) ? $sortOrder : 1;
            $data['sort_order'] = $sortOrder;
            $connection->insert($this->resource->getTableName('eav_entity_attribute'), $data);
        }

        return $this;
    }
}
