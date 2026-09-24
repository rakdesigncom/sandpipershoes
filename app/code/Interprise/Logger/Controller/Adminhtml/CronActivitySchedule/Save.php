<?php

namespace Interprise\Logger\Controller\Adminhtml\CronActivitySchedule;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $activityshedule;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->activityshedule = $activityScheduleFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('cronactivityschedule_id');

            $model = $this->activityshedule->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Cronactivityschedule no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Cronactivityschedule.'));
                $this->dataPersistor->clear('interprise_logger_cronactivityschedule');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['cronactivityschedule_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Cronactivityschedule.'));
            }

            $this->dataPersistor->set('interprise_logger_cronactivityschedule', $data);

            return $resultRedirect->setPath('*/*/edit', [
                'cronactivityschedule_id' => $this->getRequest()->getParam('cronactivityschedule_id'),
            ]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
