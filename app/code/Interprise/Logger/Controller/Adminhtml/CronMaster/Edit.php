<?php

namespace Interprise\Logger\Controller\Adminhtml\CronMaster;

class Edit extends \Interprise\Logger\Controller\Adminhtml\CronMaster
{

    protected $resultPageFactory;

    protected $cronmasterfactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Interprise\Logger\Model\CronMasterFactory $cronMasterFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cronmasterfactory = $cronMasterFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('cronmaster_id');
        $model = $this->cronmasterfactory->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Cronmaster no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('interprise_logger_cronmaster', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Cronmaster') : __('New Cronmaster'),
            $id ? __('Edit Cronmaster') : __('New Cronmaster')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cronmasters'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Cronmaster'));

        return $resultPage;
    }
}
