<?php

namespace Interprise\Logger\Controller\Adminhtml\Diagnostics;

use mysql_xdevapi\BaseResult;

class Ajaxcall extends \Magento\Backend\App\Action
{
    public $connn;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_datahelper;

    protected $_curl;

    protected $_customerfactory;

    protected $_installwizardFactory;

    protected $_activityschedeuleFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Interprise\Logger\Helper\Data $datahelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Interprise\Logger\Model\InstallwizardFactory $installwizardFactory,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_datahelper = $datahelper;
        $this->_curl = $curl;
        $this->_customerfactory = $customerFactory;
        $this->_installwizardFactory = $installwizardFactory;
        $this->_activityschedeuleFactory = $activityScheduleFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout/exampleadminnewpage_helloworld_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $import_result = $this->runimport($type);
        $resultJson = $this->resultJsonFactory->create();
        if ($import_result['status']) {
            $resultJson->setData(['status' => true, 'message' => 'success']);
        } else {
            $resultJson->setData(['status' => false, 'message' => $import_result['message']]);
        }

        return $resultJson;
        // return  $resultPage = $this->resultPageFactory->create();
    }

    public function runimport($funtion_name)
    {
        switch ($funtion_name) {
            case 'import_country':
                $result = $this->importCountry();
                if (isset($result['status']) && $result['status'] != true) {
                    return $result;
                }
                break;
            case 'payment_methods':
                $result = $this->paymentMethods();
                break;
            case 'shipping_methods':
                $result = $this->shippingMethods();
                break;
            case 'customers':
                $result = $this->customers();
                break;
            case 'stock_items':
                $result = $this->stockItems();
                break;
            case 'kit_items':
                $result = $this->kitItems();
                break;
            case 'quotes':
                $result = $this->quotes();
                break;
            case 'so_order':
                $result = $this->soOrder();
                break;
            case 'crma_order':
                $result = $this->crma_order();
                break;
            case 'quotes_order':
                $result = $this->quotes_order();
                break;
            case 'invoices_orders':
                $result = $this->invoice();
                break;
            case 'credit_notes':
                $result = $this->credit_notes();
                break;
            case 'back_order':
                $result = $this->back_order();
                break;
            case 'matrix_item':
                $result = $this->matrixItem();
                break;
            case 'category':
                $result = $this->category();
                break;
            default:
                $result = ['status' => false, 'message' => 'Not defined any function which are trying to execute'];
                break;
        }

        return $result;
    }

    public function importCountry()
    {
        $counts = $this->_datahelper->populateCountryClassTable();
        if ($counts == 'fail') {
            $result = [
                'status' => false,
                'message' => 'Server not responding or network issue please check connectivity and try again later',
            ];

            return $result;
        }
        $data = [];
        $data['count'] = $counts;
        $data['status'] = "Done";
        $data['sync_done'] = "1";
        $data['action'] = "Done";
        $this->updateInstallwizard($data, 'import_country');
    }

    public function updateInstallwizard($data, $type)
    {
        $status = $data['status'];
        $action = $data['action'];
        $sync_done = '';
        if (isset($data['sync_done'])) {
            $sync_done = $data['sync_done'];
        }
        $countss = $data['count'];
        $collection_query = $this->_installwizardFactory->create()->getCollection();
        $collection_query->addFieldToFilter('function_name', ['eq' => $type]);
        foreach ($collection_query as $item_col) {
            $item_col->setData('status', $status);
            $item_col->setData('action', $action);
            $item_col->setData('sync_done', $sync_done);
            $item_col->setData('total_records', $countss);
        }
        if (isset($data['count'])) {
            $total_count = $data['count'];
            $item_col->setData('total_records', $total_count);
            //$update_query = "update interprise_install_wizard set status='$status',action='$action',
            //total_records='$total_count' where function_name='$type'";
        }
        if (isset($data['sync_done']) && $data['sync_done'] == 1) {
            $collection_query2 = $this->_installwizardFactory->create()->getCollection();
            $collection_query2->addFieldToFilter('function_name', ['eq' => $type]);
            foreach ($collection_query2 as $item_col2) {
                $item_col2->setData('sync_done', 1);
            }
            $collection_query2->save();
            //$update_query2 = "update interprise_install_wizard set sync_done='1' where function_name='$type'";
        }
        $collection_query->save();
    }

    public function paymentMethods()
    {
        $populate_payment_table = $this->_datahelper->insertPaymentMethods();

        return $populate_payment_table;
    }

    public function shippingMethods()
    {
        $populate_payment_table = $this->_datahelper->insertShippingMethods();

        return $populate_payment_table;
    }

    public function customers()
    {
        //$list_end_point="customer?onlyActive=true&page[size]=50";
        $list_end_point = "customer?onlyActive=true&page[size]=500&page[number]=22";
        $this->ismaster_id = 2;
        $this->recordsget = 0;
        $this->getCurlDataWizard($list_end_point, '');
    }

    public function getCurlDataWizard($URL, $full = '')
    {
        //echo __METHOD__; die('dfdfdfdf');
        $this->result_data = [];
        $this->api_responce = [];
        $this->request_url = [];
        $this->reattempt = true;
        $interprise_status = $this->_datahelper->getConfig('setup/general/enable');
        $interprise_api_key = $this->_datahelper->getConfig('setup/general/api_key');
        $interprise_api_url = $this->_datahelper->getConfig('setup/general/api_url');
        $this->is_status = $interprise_status;
        $this->is_api_key = $interprise_api_key;
        $this->is_api_url = $interprise_api_url;

        if ($full) {
            $URL = $full;
        } else {
            $URL = $this->is_api_url . $URL;
        }
        $this->is_next = false;
        $this->getCurlDataRequestWizard('', $URL);
    }

    public function getCurlDataRequestWizard($URL, $full = '')
    {
        $username = $this->is_api_key;
        $password = '';
        if ($full) {
            $URL = $full;
        } else {
            $URL = $this->is_api_url . $URL;
        }

        $URL = str_replace(" ", "%20", $URL);

        $headers = ["Content-Type" => "application/json"];
        $this->_curl->setHeaders($headers);
        $this->_curl->setCredentials($username, $password);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->_curl->get($URL);
        $success_result = $this->_curl->getBody();
        $success_status = $this->_curl->getStatus();
        $success_result = json_decode($success_result, true);
        if ($success_status == 200) {
            $act_time = $this->_datahelper->getCurrentTime();
            //if (array_key_exists('type', $success_result->data)) {
            if (isset($success_result['data'])
                && is_array($success_result['data'])
                && count($success_result['data']) > 0) {
                $insert_data_bulk = [];
                $count_result_data = count($success_result['data']);
                foreach ($success_result['data'] as $key => $vs) {
                    //$this->result_data[$key] = $value;
                    $attributes = $vs['attributes'];
                    if ($this->ismaster_id == 1 || $this->ismaster_id == 12 || $this->ismaster_id == 16) {
                        $dataId = $attributes['itemCode'];
                        $status_enable = $attributes['status'];
                        if (in_array($status_enable, ['A', 'P'])) {
                            $stat = 'pending';
                        } else {
                            $stat = 'Discontinued';
                        }
                    } else if ($this->ismaster_id == 2) {
                        $dataId = $attributes['customerCode'];
                        $stat = 'pending';
                    } else if ($this->ismaster_id == 3) {
                        $dataId = $attributes['salesOrderCode'];
                        $stat = 'pending';
                    } else if ($this->ismaster_id == 8) {
                        $dataId = $attributes['invoiceCode'];
                        $stat = 'pending';
                    }
                    $json_data = $dataId;
                    $activityFact = $this->_activityschedeuleFactory->create();
                    $activityFact->setData('CronLogId', $this->ismaster_id);
                    $activityFact->setData('CronMasterId', $this->ismaster_id);
                    $activityFact->setData('ActionType', 'INSERT');
                    $activityFact->setData('DataId', $dataId);
                    $activityFact->setData('JsonData', $json_data);
                    $activityFact->setData('Status', $stat);
                    $activityFact->setData('ActivityTime', $act_time);
                    $activityFact->save();
                }
                //print_r($insert_data_bulk);
                //$this->connn->insertMultiple('interprise_logger_cronactivityschedule', $insert_data_bulk);
                $this->recordsget = $this->recordsget + $count_result_data;

                $data = [];
                if (!array_key_exists('next', $success_result['links'])) {
                    $data['sync_done'] = 1;
                }
                $data['count'] = $this->recordsget;
                $data['status'] = "imported";
                $data['action'] = "imported";
                switch ($this->ismaster_id) {
                    case '1':
                        $this->updateInstallwizard($data, 'stock_items');
                        break;
                    case '2':
                        $this->updateInstallwizard($data, 'customers');
                        break;
                    case '12':
                        $this->updateInstallwizard($data, 'matrix_item');
                        break;
                    case '16':
                        $this->updateInstallwizard($data, 'kit_items');
                        break;
                    default:
                        break;
                }
            }
            if (array_key_exists('next', $success_result['links'])) {
                $this->is_next = true;
                $this->getCurlDataRequestWizard('', $success_result['links']['next']);
            }
            $this->api_responce[] = 'success';
        } else if ($success_status == 404) {
            $this->result_data[] = ['error' => "Server not responding"];
        } else if ($success_status == 0) {
            $this->result_data[] = ['error' => "Server not responding"];
        } else {
            $this->api_responce[] = 'error';
            $this->result_data[] = $success_result;
        }
    }

    public function stockItems()
    {
        $data_endpoint = [];
        //$list_end_point="inventory/ItemList/Stock?page[number]=1";
        $list_end_point = "inventory/ItemList?itemType=Stock&publishStatus=1&itemStatus=A";
        $this->ismaster_id = 1;
        $this->recordsget = 0;
        $this->getCurlDataWizard($list_end_point, '');
    }

    public function kitItems()
    {
        $data_endpoint = [];
        //$list_end_point="inventory/ItemList/kit";
        $list_end_point = "inventory/ItemList?itemType=Kit&publishStatus=1&itemStatus=A";
        $this->ismaster_id = 16;
        $this->recordsget = 0;
        $this->getCurlDataWizard($list_end_point, '');
//        }
    }

    public function soOrder()
    {
        $resource = $this->_datahelper;
        $fromdate = $resource->getConfig('setup/general/historicaldatafrom');
        // $list_end_point="salesorder?dateFrom=";
        // $list_end_point.= "$fromdate T00:00:00&dateTo=2099-12-20T10:13:00";
        // $list_end_point.="&activeCustomerOnly=true&isVoided=false&page[number]=1";
        $customerArray = ['CUST-010797', 'CUST-010798', 'CUST-010799', 'CUST-010800', 'CUST-015424', 'CUST-019400', 'CUST-020364'];
        foreach ($customerArray as $customerCode) {
            $list_end_point = "customer/salesorder?customerCode=$customerCode";
            $this->ismaster_id = 3;
            $this->recordsget = 0;
            $this->getCurlDataWizard($list_end_point, '');
        }
//                    $list_end_point="customer/salesorder?customerCode=CUST-015424";

    }

    public function invoice()
    {
        $resource = $this->_datahelper;
        $fromdate = $resource->getConfig('setup/general/historicaldatafrom');
        // $list_end_point="invoice?dateFrom=";
        // $list_end_point.= $fromdate;
        // $list_end_point.="T00:00:00&dateTo=2099-12-20T10:13:00&activeCustomerOnly=true";
        // $list_end_point.="&isVoided=false&page[number]=1";
//                $list_end_point="customer/invoice?customerCode=CUST-015424";
        $customerArray = ['CUST-010797', 'CUST-010798', 'CUST-010799', 'CUST-010800', 'CUST-015424', 'CUST-019400', 'CUST-020364'];
        foreach ($customerArray as $customerCode) {
            $list_end_point = "customer/invoice?customerCode=$customerCode";
            $this->ismaster_id = 8;
            $this->recordsget = 0;
            $this->getCurlDataWizard($list_end_point, '');
        }
    }

    public function matrixItem()
    {
        $list_end_point = "inventory/ItemList/matrix group?departmentCode=Sandpiper";
        $this->ismaster_id = 12;
        $this->recordsget = 0;
        $this->getCurlDataWizard($list_end_point, '');
    }

    public function category()
    {
        $create_category_structure = $this->_datahelper->createCategorystructure2();

        return $create_category_structure;
    }

    public function processCronActivityScheduleImportdata($data, $master_id)
    {
        switch ($master_id) {
            case 1:
            case 16:
                //$insert_query  ="insert into interprise_logger_cronactivityschedule(CronLogId,CronMasterId,
                //ActionType,DataId,Status) values(12121,3,'INSERT','ITEM-00001','pending')";
                // $connnections->query($insert_query);
                $cron_log_id = 1111111;
                foreach ($data as $k => $vs) {
                    $attributes = $vs['attributes'];
                    $dataId = $attributes['itemCode'];
                    $status_enable = $attributes['status'];
                    $json_data = json_encode($attributes);
                    $activityFact = $this->_activityschedeuleFactory->create();
                    $activityFact->setData('CronLogId', $cron_log_id);
                    $activityFact->setData('CronMasterId', $master_id);
                    $activityFact->setData('ActionType', 'INSERT');
                    $activityFact->setData('DataId', $dataId);
                    $activityFact->setData('JsonData', $json_data);
                    if (in_array($status_enable, ['A', 'P'])) {
                        $activityFact->setData('Status', 'pending');
                    } else {
                        $activityFact->setData('Status', 'Discontinued');
                    }
                    $activityFact->setData('ActivityTime', $this->_datahelper->getCurrentTime());
                    $activityFact->save();
                }

                break;
            case 2:
                $cron_log_id = 222222222;
                foreach ($data as $k => $vs) {
                    $attributes = $vs['attributes'];
                    $dataId = $attributes['customerCode'];
                    $status_enable = $attributes['isActive'];
                    $json_data = json_encode($attributes);
                    $activityFact = $this->_activityschedeuleFactory->create();
                    $activityFact->setData('CronLogId', $cron_log_id);
                    $activityFact->setData('CronMasterId', $master_id);
                    $activityFact->setData('ActionType', 'INSERT');
                    $activityFact->setData('DataId', $dataId);
                    $activityFact->setData('JsonData', $json_data);
                    $activityFact->setData('Status', 'pending');
                    $activityFact->setData('ActivityTime', $this->_datahelper->getCurrentTime());
                    $activityFact->save();
                }
                break;
            case 3:
                $cron_log_id = 333333333;
                foreach ($data as $k => $vs) {
                    $attributes = $vs['attributes'];
                    $dataId = $attributes['salesOrderCode'];
                    $json_data = json_encode($attributes);
                    $activityFact = $this->_activityschedeuleFactory->create();
                    $activityFact->setData('CronLogId', $cron_log_id);
                    $activityFact->setData('CronMasterId', $master_id);
                    $activityFact->setData('ActionType', 'INSERT');
                    $activityFact->setData('DataId', $dataId);
                    $activityFact->setData('JsonData', $json_data);
                    $activityFact->setData('Status', 'pending');
                    $activityFact->setData('ActivityTime', $this->_datahelper->getCurrentTime());
                    $activityFact->save();
                }
                break;
            case 8:
                $cron_log_id = 8888888888;
                foreach ($data as $k => $vs) {
                    $attributes = $vs['attributes'];
                    $dataId = $attributes['invoiceCode'];
                    $json_data = json_encode($attributes);
                    $activityFact = $this->_activityschedeuleFactory->create();
                    $activityFact->setData('CronLogId', $cron_log_id);
                    $activityFact->setData('CronMasterId', $master_id);
                    $activityFact->setData('ActionType', 'INSERT');
                    $activityFact->setData('DataId', $dataId);
                    $activityFact->setData('JsonData', $json_data);
                    $activityFact->setData('Status', 'pending');
                    $activityFact->setData('ActivityTime', $this->_datahelper->getCurrentTime());
                    $activityFact->save();
                }
                break;

            default:
                break;
        }
    }
}
