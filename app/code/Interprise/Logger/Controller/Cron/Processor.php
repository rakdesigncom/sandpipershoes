<?php

namespace Interprise\Logger\Controller\Cron;

class Processor extends \Magento\Framework\App\Action\Action
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

    protected $customer;

    protected $prices;

    protected $stock;

    protected $pushcustomeraddress;

    protected $salesorder;

    protected $pushsalesorder;

    protected $_activityScFactory;

    protected $state;

    protected $inventoryItemdepartment;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Interprise\Logger\Helper\Data $_helper,
        \Interprise\Logger\Helper\Customers $_customer,
        \Interprise\Logger\Helper\Prices $_prices,
        \Interprise\Logger\Helper\InventoryStock $_stock,
        \Interprise\Logger\Helper\Pushcustomer $_pushcustomer,
        \Interprise\Logger\Helper\Pushcustomeraddress $_pushcustomeraddress,
        \Interprise\Logger\Helper\Salesorder $_salesorder,
        \Interprise\Logger\Helper\Invoice $invoice,
        \Interprise\Logger\Helper\Salesorderworkflow $_salesorderworkflow,
        \Interprise\Logger\Helper\Pushsalesorder $_pushsalesorder,
        \Interprise\Logger\Helper\CustomerSpecialPrice $_customerspecialprice,
        \Interprise\Logger\Helper\Pushcrm $pushcrm,
        \Interprise\Logger\Helper\CRM $crm,
        \Interprise\Logger\Helper\InventoryPricingLevel $invpricinglevel,
        \Interprise\Logger\Helper\InventoryItem $_inventoryitem,
        \Interprise\Logger\Helper\InventoryMatrixItem $_inventoryMatrixItem,
        \Interprise\Logger\Helper\Customershipto $customershipto,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Interprise\Logger\Helper\OrderReattempt $_orderReattempt,
        \Interprise\Logger\Helper\InventoryItemDepartment $_inventoryItemdepartment,
        \Magento\Framework\App\State $appstate
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $_helper;
        $this->customer = $_customer;
        $this->prices = $_prices;
        $this->stock = $_stock;
        $this->pushcustomer = $_pushcustomer;
        $this->pushcustomeraddress = $_pushcustomeraddress;
        $this->salesorder = $_salesorder;
        $this->_invoice = $invoice;
        $this->salesorderworkflow = $_salesorderworkflow;
        $this->pushsalesorder = $_pushsalesorder;
        $this->customerspecialprice = $_customerspecialprice;
        $this->_pushcrm = $pushcrm;
        $this->_crm = $crm;
        $this->_invpricinglevel = $invpricinglevel;
        $this->_customershipto = $customershipto;
        $this->_activityScFactory = $activityScheduleFactory;
        $this->inventoryitem = $_inventoryitem;
        $this->state = $appstate;
        $this->inventoryMatrixItem = $_inventoryMatrixItem;
        $this->reattemptorder = $_orderReattempt;
        $this->inventoryItemdepartment = $_inventoryItemdepartment;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (isset($_REQUEST['master_id']) && $_REQUEST['master_id'] == 4) {
            echo '<br/>Only order post';
            $this->state->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_ADMINHTML,
                [$this, "executeCallBackOrder"]
            );
        } else {
            echo '<br/>without order post';
            $this->state->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_ADMINHTML,
                [$this, "executeCallBack"]
            );
        }
    }

    public function executeCallBackOrder()
    {
        $data = $this->getItemOrder();
        if (is_array($data) && count($data) > 0) {
            do {
                $master_id = $data['CronMasterId'];
                switch ($master_id) {
                    case 4:
                        $result = $this->pushsalesorder->pushsalesorderSingle($data);
                        $this->reattemptorder->updateFailedOrdersData($result, $data);
                        break;
                }
                $this->updateProcessedStatus($result, $data);
                $data = $this->getItemOrder();
            } while (count($data) > 0);
        }
    }

    public function getItemOrder()
    {
        $result = [];
        $collections = $this->_activityScFactory->create()->getCollection()
            ->addFieldToFilter('status', ['eq' => 'pending'])
            ->addFieldToFilter('CronMasterId', ['eq' => '4'])
            ->setOrder('priority', 'ASC')
            ->setOrder('cronactivityschedule_id', 'ASC')
            ->setPageSize(1)->setCurPage(1);
        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();
        }
        if (isset($result['DataId'])) {
            $collections_update = $this->_activityScFactory->create()->getCollection()
                ->addFieldToFilter('DataId', ['eq' => $result['DataId']])
                ->addFieldToFilter('status', ['eq' => 'pending'])
                ->addFieldToFilter('CronMasterId', ['eq' => $result['CronMasterId']]);
            if ($collections_update->count() > 0) {
                foreach ($collections_update as $item_update) {
                    $item_update->setData('Status', 'In Process');
                }
                $collections_update->save();
            }

            return $result;
        } else {
            return [];
        }
    }

    public function updateProcessedStatus($result, $data)
    {
        $data_id = $data['DataId'];
        $master_id = $data['CronMasterId'];
        $status = $result['Status'];
        $remarks = $result['Remarks'];
        if (isset($result['Request'])) {
            $request = $result['Request'];
        } else {
            $request = "";
        }
        $response = $result['Response'];
        $activity_time = $result['ActivityTime'];

        $collections = $this->_activityScFactory->create()->getCollection()
            ->addFieldToFilter('DataId', ['eq' => "$data_id"])
            ->addFieldToFilter('CronMasterId', ['eq' => $master_id])
            ->addFieldToFilter('Status', ['eq' => 'In Process']);
        foreach ($collections as $item) {
            $item->setData('Status', $status);
            $item->setData('Remarks', $remarks);
            $item->setData('Request', $request);
            $item->setData('Response', $response);
            if (isset($result['JsonData'])) {
                $item = $item->setData('JsonData', $result['JsonData']);
            }
            $item->setData('ActivityTime', $activity_time);
            $item->save();
        }
    }

    public function executeCallBack()
    {
        $data = $this->getItem();
        if (is_array($data) && count($data) > 0) {
            //$this->interprise_processor($processor_id, 1);
            do {
                $master_id = $data['CronMasterId'];
                switch ($master_id) {
                    case 1:
                        $a = 1;
                        $result = $this->inventoryitem->inventoryItemSingle($data);
                        if (isset($result['TypeProduct']) && $result['TypeProduct'] == 'matrix group') {
                            $result = $this->inventoryMatrixItem->InventoryMatrixItem_single($data);
                        }
                        break;
                    case 2:
                        $result = $this->customer->customerSingle($data);
                        break;
                    case 3:
                        $result = $this->salesorder->salesorderSingle($data);
                        break;
//                    case 4:
//                        $result = $this->pushsalesorder->pushsalesorderSingle($data);
//                        break;
                    case 5:
                        $result = $this->prices->pricesSingle($data);
                        break;
                    case 7:
                        $result = $this->stock->inventoryStockSingle($data);
                        break;
                    case 8:
                        $result = $this->_invoice->invoiceSingle($data);
                        break;
                    case 11:
                        $result = $this->_crm->crmSingle($data);
                        break;
                    case 12:
                        $result = $this->inventoryMatrixItem->InventoryMatrixItem_single($data);
                        $a = 1;
                        break;
                    case 13:
                        $result = $this->salesorderworkflow->salesorderworkflowSingle($data);
//                        echo '<pre>';
                        break;
                    case 16:
                        //$result = $this->inventoryBundleItem->InventoryBundleItem_single($data);
                        $a = 1;
                        break;
                    case 17:
                        $result = $this->_pushcrm->pushcrmSingle($data);
                        break;
                    case 18:
                        $result = $this->customerspecialprice->customerSpecialPriceSingle($data);
                        break;
                    case 19:
                        $result = $this->_customershipto->customershiptoSingle($data);
                        break;
                    case 21:
                        $result = $this->pushcustomeraddress->pushcustomeraddressSingle($data);
                        break;
                    case 22:
                        $result = $this->_invpricinglevel->inventoryPricingLevelSingle($data);
                        break;
                    case 26:
                        //$result = $this->pushcustomer->pushcustomerSingle($data);
                        break;
                    case 30:
                        $result = $this->inventoryitem->inventoryCustomUpdateSingle($data);
                        break;
                    case 31:
                        $result = $this->inventoryItemdepartment->inventoryItemDepartmentSingle($data);
                        break;
                    case 32:
                        $result = $this->inventoryitem->inventoryItemDescriptionSingle($data);
                        break;
                    case 33:
                        $result = $this->inventoryitem->inventoryItemWebOptionSingle($data);
                        break;
                    case 34:
                        $result = $this->inventoryitem->inventoryUnitMeasureSingle($data);
                        break;
                    case 35:
                        $result = $this->inventoryitem->itemSpecialPriceSingle($data);
                        break;
                    case 36:
                        $result = $this->inventoryitem->inventoryCategorySingle($data);
                        break;
                    case 37:
                        $result = $this->inventoryitem->categoryWebOptionSingle($data);
                        break;
                    case 38:
                        $result = $this->inventoryMatrixItem->InventoryMatrixItem_single($data);
                        break;
                    default:
                        break;
                }
                // echo '<pre>';
                // print_r($result);
                // echo '</pre>';


                $this->updateProcessedStatus($result, $data);
                $data = $this->getItem();
//                die("SDA");
            } while (count($data) > 0);
        }
    }

    public function getItem()
    {
        $result = [];
        $collections = $this->_activityScFactory->create()->getCollection()
            ->addFieldToFilter('status', ['eq' => 'pending'])
            ->addFieldToFilter('CronMasterId', ['neq' => '4'])
            ->setOrder('priority', 'ASC')
            ->setOrder('cronactivityschedule_id', 'ASC')
            ->setPageSize(1)->setCurPage(1);
        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();
        }
        if (isset($result['DataId'])) {
            $collections_update = $this->_activityScFactory->create()->getCollection()
                ->addFieldToFilter('DataId', ['eq' => $result['DataId']])
                ->addFieldToFilter('status', ['eq' => 'pending'])
                ->addFieldToFilter('CronMasterId', ['eq' => $result['CronMasterId']]);
            if ($collections_update->count() > 0) {
                foreach ($collections_update as $item_update) {
                    $item_update->setData('Status', 'In Process');
                }
                $collections_update->save();
            }

            return $result;
        } else {
            return [];
        }
    }

}
