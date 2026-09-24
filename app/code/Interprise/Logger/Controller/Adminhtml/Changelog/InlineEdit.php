<?php

namespace Interprise\Logger\Controller\Adminhtml\Changelog;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;
    protected $changelogfact;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->changelogfact = $changelogFactory;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    /** @var \Interprise\Logger\Model\Changelog $model */
                    $model = $this->changelogfact->create()->load($modelid);
                    try {
                        $setdatas = $this->mergefunc($model->getData(), $postItems[$modelid]);
                        $model->setData($setdatas);
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Changelog ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }

    public function mergefunc($arr1, $arr2)
    {
        return array_merge($arr1, $arr2);
    }
}
