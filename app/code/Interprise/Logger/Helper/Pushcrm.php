<?php

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

class Pushcrm extends Data
{

    /**
     * @return void
     */
    public $connection;
    public $resource;
    public $customer;

    protected $activityschdule;
    protected $casefactory;
    protected $customerFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryobj,
        \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer,
        \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CountryclassmappingFactory $classmapping,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory,
        \Interprise\Logger\Model\CasesFactory $caseFactory
    ) {
        $this->customer  = $customer;
        $this->customerFactory  = $customerFactory;
        $this->activityschdule = $activityScheduleFactory;
        $this->casefactory = $caseFactory;
        parent::__construct(
            $context,
            $httpContext,
            $product,
            $curl,
            $datetime,
            $categorycollection,
            $productCollectionFactory,
            $categoryobj,
            $pricingcustomer,
            $pricelistsFactory,
            $productFactory,
            $session,
            $classmapping,
            $statementaccountFactory,
            $addressFactory,
            $custompaymentFactory,
            $custompaymentitemFactory,
            $paymentmethodfact,
            $installwizardFactory,
            $shippingstoreinterpriseFactory,
            $curlFactory
        );
    }
    public function updateCronActivitySchedule($datas, $cron_log_id)
    {
        $get_result_data = $datas;
         $table_activity_schedule = 'interprise_logger_cronactivityschedule';
        if (count($get_result_data) > 0) {
            foreach ($get_result_data as $ks => $_changeolog_detail) {
                $customer_id = $_changeolog_detail['customer_id'];
                $customer_obj = $this->customerFactory->create()->load($customer_id);
                if ($customer_obj->getInterpriseCustomerCode()=='') {
                    $remark_message = 'Some error occured customer  still not posted
                    in Interprise(Interprise Cusotmer code not found in Magento)';
                    $activitschedulmodel = $this->activityschdule->create();
                    $activitschedulmodel->setData('CronLogId', $cron_log_id);
                    $activitschedulmodel->setData('CronMasterId', 17);
                    $activitschedulmodel->setData('ActionType', 'POST');
                    $activitschedulmodel->setData('ActivityTime', $this->get_current_time());
                    $activitschedulmodel->setData('DataId', $_changeolog_detail['entity_id']);
                    $activitschedulmodel->setData('Status', 'fail');
                    $activitschedulmodel->setData('Remarks', $remark_message);
                    $activitschedulmodel->setData('JsonData', '');
                    $activitschedulmodel->save();
                } else {
                    $result_jso_array = $this->createJson(
                        $_changeolog_detail,
                        $customer_obj->getInterpriseCustomerCode()
                    );
                    $activitschedulmodel = $this->activityschdule->create();
                    $activitschedulmodel->setData('CronLogId', $cron_log_id);
                    $activitschedulmodel->setData('CronMasterId', 17);
                    $activitschedulmodel->setData('ActionType', 'POST');
                    $activitschedulmodel->setData('ActivityTime', $this->get_current_time());
                    $activitschedulmodel->setData('DataId', $_changeolog_detail['entity_id']);
                    $activitschedulmodel->setData('Status', 'pending');
                    $activitschedulmodel->setData('Remarks', 'pending');
                    $activitschedulmodel->setData('JsonData', json_encode($result_jso_array));
                    $activitschedulmodel->save();
                }

                 $entity_id = $_changeolog_detail['entity_id'];
                $casemodel = $this->casefactory->create()->load($entity_id);
                $casemodel->setData('from_created', 1);
                $casemodel->save();
            }
        }
    }
    public function createJson($data, $customer_code)
    {
        $json_array['type']='Cases';
        $json_array['customerCode']=$customer_code;
        $json_array['subject']=$data['subject'];
        $json_array['startDate']=$data['created_at'];
        $json_array['problemDescription']=$data['description'];
        $json_array['priority']=$data['priority'];
        $json_array['activity']='Waiting to be assigned';
        return $json_array;
    }
    public function pushcrmSingle($datas)
    {
        //echo __Method__;
        $update_data=[];
        $dataId = $datas['DataId'];
        $json = $datas['JsonData'];
        try {
            $update_data['ActivityTime']=$this->get_current_time();
            $result=$this->postCurlData('crm/case', $json);
            $update_data['Request']=$result['request'];
            if (!$result['api_error']) {
                $update_data['Status']='fail';
                $update_data['Response']=json_encode($result['result'], JSON_UNESCAPED_SLASHES);
                $update_data['Remarks']=json_encode($result['result'], JSON_UNESCAPED_SLASHES);
                return $update_data;
            }
            $update_data['Response']=json_encode($result['result']);
            if (is_array($result['result']) && array_key_exists('errors', json_decode($result['result'], true))) {
                $update_data['Status']='fail';
                $update_data['Remarks']=json_encode($result['result'], JSON_UNESCAPED_SLASHES);
                return $update_data;
            } else {
                $case_id="ACT".filter_var($result['result'], FILTER_SANITIZE_NUMBER_INT);
                try {
                    $casemodel = $this->casefactory->create()->load($dataId);
                    $casemodel->setData('case_number', $case_id);
                    $casemodel->save();
                    $update_data['Status']='Success';
                    $update_data['Remarks']='Success';
                } catch (exception $e) {
                    $remarkmessage = "Urgent:Cases posted but not updated in magento table have
                    to look on priority because it may be post multiple time in Interprise";
                    $update_data['Status'] = 'fail';
                    $update_data['Remarks'] = $remarkmessage;
                }
            }

        } catch (Exception $ex) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $ex->getMessage();
        }
        return $update_data;
    }
}
