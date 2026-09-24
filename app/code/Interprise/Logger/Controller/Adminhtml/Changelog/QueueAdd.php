<?php

namespace Interprise\Logger\Controller\Adminhtml\Changelog;

class QueueAdd extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Interprise\Logger\Model\Order\SyncQueue
     */
    protected $orderSyncQueue;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Interprise\Logger\Model\Order\SyncQueue $orderSyncQueue
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Interprise\Logger\Model\Order\SyncQueue $orderSyncQueue,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->orderSyncQueue = $orderSyncQueue;
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
            $orderId = $this->getRequest()->getParam('order_id');
            if (!$orderId) {
                $messages[] = __('Order not found.');
                $error = true;
            } else {
                try {
                    $this->orderSyncQueue->add($orderId);

                    $this->messageManager->addSuccessMessage(__('Order added to queue.'));
                } catch (\Exception $e) {
                    $messages[] = "Error add to queue: " . $e->getMessage();
                    $error = true;
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
