<?php

namespace Interprise\Logger\Controller\Adminhtml\CronLog;

class Delete extends \Interprise\Logger\Controller\Adminhtml\CronLog
{
    protected $resultPageFactory;

    protected $cronlogFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Interprise\Logger\Model\CronLogFactory $cronLogFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Interprise\Logger\Model\CronLogFactory $cronLogFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cronlogFactory = $cronLogFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('cronlog_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->cronlogFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Cronlog.'));

                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());

                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['cronlog_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Cronlog to delete.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
