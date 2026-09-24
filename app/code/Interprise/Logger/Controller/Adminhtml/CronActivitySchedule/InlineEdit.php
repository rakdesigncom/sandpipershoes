<?php

namespace Interprise\Logger\Controller\Adminhtml\CronActivitySchedule;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;

    protected $activityshedule;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->activityshedule = $activityScheduleFactory;
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
                    /** @var \Interprise\Logger\Model\CronActivitySchedule $model */
                    $model = $this->activityshedule->create()->load($modelid);
                    try {
                        $setdatas = $this->mergefunc($model->getData(), $postItems[$modelid]);
                        $model->setData($setdatas);
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Cronactivityschedule ID: {$modelid}]  {$e->getMessage()}";
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
