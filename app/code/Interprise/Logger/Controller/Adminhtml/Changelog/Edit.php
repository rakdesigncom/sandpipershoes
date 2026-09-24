<?php

namespace Interprise\Logger\Controller\Adminhtml\Changelog;

class Edit extends \Interprise\Logger\Controller\Adminhtml\Changelog
{

    protected $resultPageFactory;

    protected $changelogfact;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
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
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('changelog_id');
        $model = $this->changelogfact->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Changelog no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('interprise_logger_changelog', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Changelog') : __('New Changelog'),
            $id ? __('Edit Changelog') : __('New Changelog')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Changelogs'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Changelog'));

        return $resultPage;
    }
}
