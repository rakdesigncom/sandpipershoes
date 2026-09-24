<?php

namespace Interprise\Logger\Controller\Adminhtml\CronActivitySchedule;

class Edit extends \Interprise\Logger\Controller\Adminhtml\CronActivitySchedule
{

    protected $resultPageFactory;

    protected $activityshedule;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->activityshedule = $activityScheduleFactory;
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
        $id = $this->getRequest()->getParam('cronactivityschedule_id');
        $model = $this->activityshedule->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Cronactivityschedule no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('interprise_logger_cronactivityschedule', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Cronactivityschedule') : __('New Cronactivityschedule'),
            $id ? __('Edit Cronactivityschedule') : __('New Cronactivityschedule')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cronactivityschedules'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Cronactivityschedule'));

        return $resultPage;
    }
}
