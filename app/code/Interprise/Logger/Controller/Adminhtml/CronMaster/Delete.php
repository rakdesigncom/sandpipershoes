<?php

namespace Interprise\Logger\Controller\Adminhtml\CronMaster;

class Delete extends \Interprise\Logger\Controller\Adminhtml\CronMaster
{
    protected $resultPageFactory;

    protected $cronmasterfactory;

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
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('cronmaster_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->cronmasterfactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Cronmaster.'));

                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());

                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['cronmaster_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Cronmaster to delete.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
