<?php

namespace Interprise\Logger\Controller\Cron;

class Reattemptcron extends \Magento\Framework\App\Action\Action
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactor
     */
    public $is_status;

    public $is_api_key;

    public $is_api_url;

    public $helper;

    public $_customer;

    public $pushcustomer;

    public $salesorderworkflow;

    public $_invoice;

    public $_pushcrm;

    public $_crm;

    public $_invpricinglevel;

    public $_customershipto;

    public $inventoryitem;

    public $inventoryMatrixItem;

    public $customerspecialprice;

    // public $pushsalesorder;
    // public $_prices;

    public $connection;

    public $resource;

    public $reattemptorder;

    protected $resultPageFactory;

    protected $_failedordersctory;

    protected $_activityScFactory;

    protected $state;

    protected $changelog;

    protected $_ordercolectFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Interprise\Logger\Helper\Data $_helper,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Interprise\Logger\Helper\OrderReattempt $_orderReattempt,
        \Interprise\Logger\Model\FailedOrdersFactory $failedOrdersFactory,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\State $appstate
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $_helper;
        $this->_failedordersctory = $failedOrdersFactory;
        $this->_activityScFactory = $activityScheduleFactory;
        $this->state = $appstate;
        $this->reattemptorder = $_orderReattempt;
        $this->changelog = $changelogFactory;
        $this->_ordercolectFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_processor_active = false;
        $this->_processor_active_order = false;
        $GET_CRON_RESULT = $this->_failedordersctory->create()->getCollection();
        $GET_CRON_RESULT->addFieldToFilter('Status', ['eq' => 'Fail']);
        if ($GET_CRON_RESULT->count() > 0) {
            foreach ($GET_CRON_RESULT as $key => $_crons) {
                $_cron = $_crons->getData();
                $timemagento = $this->helper->getCurrentTime();
                //echo $timemagento;
                $fromtimetable = $_cron['Next_attempt'];
                $strtotime_now = strtotime($timemagento);
                $strtotime_from = strtotime($fromtimetable);
                if ($strtotime_now > $strtotime_from) {
                    $this->proccessFailedOrders($_cron);
                }
            }
        } else {
            echo "Records not found!!";
        }

        //////////// Check orders not received in Changelog ///////////////

        //$from = '2021-03-17 00:00:00';
        //$to = '2022-03-18 25:00:00';
        //$startDate = date("Y-m-d h:i:s",strtotime('2021-3-1')); // start date
        $currentDate = $this->helper->getCurrentTime();

        $startDate = date("Y-m-d h:i:s", strtotime($currentDate) - (30 * 60));
        $endDate = date("Y-m-d h:i:s", strtotime($currentDate) - (15 * 60)); // end date
        $salesOrders = $this->getSalesOrderList($startDate, $endDate);
        if (is_array($salesOrders)) {
            foreach ($salesOrders as $key => $order) {
                $found = $this->reattemptorder->getChangelogIdByItemId($order['entity_id']);
                if (!$found) {
                    $this->insertChangelogData($order['entity_id']);
                }
            }
        }

        ///////////////////// Process to handle changelog records left in -1 //////////////////////

        $changelogInProcess = $this->reProcessChangelogInProcess($startDate, $endDate);
        print_r($changelogInProcess);

        /////////////////////////////////////////////////////////////////////////
        echo "Success";
        ///////////////////////////////////////////////////////////////////

    }

    public function proccessFailedOrders($data)
    {
        try {
            if (is_array($data) && count($data) > 0) {
                $orderIncrementId = $data['Increment_id'];
                $orderId = $data['Changelog_item_id'];
                if ($orderIncrementId != '') {
                    $shortname_store = $this->helper->getConfig('setup/general/abbr');
                    $poCode = $shortname_store . ' %23' . $orderIncrementId;

                    $orderFound = $this->checkOrderOnAPI($poCode);
                    if ($orderFound) {
                        echo "Order id " . $orderId . " deleted from failed orders." .
                            $this->deleteFailedOrderByDataId($orderId);
                    } else {
                        echo "Order id " . $orderId . " updated in changelog" .
                            $this->updateFailedOrderStatus($data['failedorder_id'], "Attempting");
                        $this->updateChangelogStatus($data['Changelog_id'], 0);
                    }
                }
            }
        } catch (Exception $e) {
        }
    }

    public function checkOrderOnAPI($poCode)
    {
        $api_responsc = $this->helper->getCurlData('salesorder?poCode=' . $poCode);
        if ($api_responsc['api_error']) {
            $data_order = $api_responsc['results']['data'];
            if (count($data_order) > 0) {
                return true;
            }
        }

        return false;
    }

    public function deleteFailedOrderByDataId($data_id)
    {
        if ($data_id != '') {
            try {
                $modelFailedOrder = $this->_failedordersctory->create()->load($data_id, 'Changelog_item_id');
                $modelFailedOrder->delete();

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function updateFailedOrderStatus($id, $newStatus)
    {
        $modelFailedOrder = $this->_failedordersctory->create()->load($id);
        $lastAttempt = $modelFailedOrder->getData('Attempt_no');
        $modelFailedOrder->setData('Status', $newStatus);
        //$modelFailedOrder->setData('Attempt_no', $lastAttempt+1);
        $modelFailedOrder->save();
    }

    public function updateChangelogStatus($id, $newStatus)
    {
        $model_changelog = $this->changelog->create()->load($id);
        $model_changelog->setData('PushedStatus', $newStatus);
        $model_changelog->save();
    }

    public function getSalesOrderList($startDate, $endDate)
    {
        $collec = $this->_ordercolectFactory->create()
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('status', ["eq" => 'processing'])
            ->addAttributeToFilter('created_at', ['from' => $startDate, 'to' => $endDate]);
        if ($collec->count() > 0) {
            $data = $collec->getData();

            //echo $data['entity_id'];
            return $data;
        } else {
            return 0;
        }
    }

    public function insertChangelogData($orderId)
    {
        $model = $this->changelog->create();
        $model->setData('CreatedAt', $this->helper->getCurrentTime());
        $model->setData('ItemType', 'order');
        $model->setData('ItemId', $orderId);
        $model->setData('Action', 'POST');
        $model->setData('PushedStatus', '0');
        $model->save();

        return true;
    }

    public function reProcessChangelogInProcess($startDate, $endDate)
    {
        $collections = $this->changelog->create()->getCollection()
            ->addFieldToFilter('PushedStatus', ['eq' => "-1"])
            ->addFieldToFilter('ItemType', ['eq' => "order"])
            ->addFieldToFilter('CreatedAt', ['from' => $startDate, 'to' => $endDate]);
        //->setPageSize(1)->setCurPage(1);
        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();
        }

        if (isset($result['changelog_id'])) {
            $collections_update = $this->changelog->create()->getCollection()
                ->addFieldToFilter('changelog_id', ['eq' => $result['changelog_id']])
                ->addFieldToFilter('ItemId', ['eq' => $result['ItemId']]);
            if ($collections_update->count() > 0) {
                foreach ($collections_update as $item_update) {
                    $item_update->setData('PushedStatus', '0');
                }
                $collections_update->save();
            }

            return $result;
        } else {
            return [];
        }
    }

}
