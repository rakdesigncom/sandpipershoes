<?php


namespace Interprise\Logger\Controller\Adminhtml\Changelog;

class Delete extends \Interprise\Logger\Controller\Adminhtml\Changelog
{
    protected $changelogfact;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->changelogfact = $changelogFactory;
        $this->resultPageFactory = $resultPageFactory;
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
        $id = $this->getRequest()->getParam('changelog_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->changelogfact->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Changelog.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['changelog_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Changelog to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
