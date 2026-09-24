<?php

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

class CRM extends Data
{
    public $datahelper;

    public $storeManager;

    public $customerFactory;

    public $connection;

    public $resource;

    public $casecolFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Interprise\Logger\Model\ResourceModel\Cases\CollectionFactory $caseFactory
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->casecolFactory = $caseFactory;
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

    public function crmSingle($datas)
    {
        $dataId = $datas['DataId'];
        $update_data['ActivityTime'] = $this->get_current_time();
        $api_responsc = $this->getCurlData('crm/activity?activityCode=' . $dataId);
        $update_data['Request'] = $api_responsc['request'];
        $update_data['Response'] = json_encode($api_responsc['results'], JSON_UNESCAPED_SLASHES);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'fail';

            return $update_data;
        }
        $result_creation = $this->createCase($api_responsc);
        if (!$result_creation['Status']) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = json_encode($result_creation['error']);
        }
        $update_data['Status'] = 'Success';
        $update_data['Remarks'] = 'Success';

        return $update_data;
    }

    public function createCase($data)
    {
        $attribute_data = $data['results']['data'][0]['attributes'];
        $customer_id = $this->isCustomerExistByInterpriseCode($attribute_data['entityCode']);
        if (!$customer_id) {
            $status['Status'] = false;
            $status['error'] = 'Customer not exist in Magento';
            $status['entity_id'] = '';

            return $status;
        }
        if (isset($attribute_data['detailsText'])) {
            $insert_data['description'] = $attribute_data['detailsText'];
        }
        $insert_data['store_id'] = '0';
        $insert_data['case_number'] = $attribute_data['activityCode'];
        $insert_data['subject'] = $attribute_data['subject'];
        $insert_data['created_at'] = $attribute_data['dateCreated'];
        $insert_data['updated_at'] = $attribute_data['dateModified'];
        $insert_data['due_at'] = $attribute_data['dueDate'];
        if (isset($attribute_data['endDate'])) {
            $insert_data['end_at'] = $attribute_data['endDate'];
        }
        if (isset($attribute_data['priority'])) {
            $insert_data['priority'] = $attribute_data['priority'];
        }
        $insert_data['status'] = str_replace("Completed", 'complete', trim($attribute_data['status']));
        $insert_data['customer_id'] = $customer_id;
        $insert_data['from_created'] = 1;
        $interprise_case = 'interprise_case';
        $case_etity_id = $this->caseExists($attribute_data['activityCode']);
        try {
            if ($case_etity_id) {
                $where = "entity_id='$case_etity_id'";
                $last_id = $this->connection->update($interprise_case, $insert_data, $where);
            } else {
                $last_id = $this->connection->insert($interprise_case, $insert_data);
            }
            $status['Status'] = true;
            $status['error'] = '';
            $status['entity_id'] = $last_id;

            return $status;
        } catch (Exception $e) {
            $status['Status'] = false;
            $status['error'] = json_encode(['crm_details' => $e->getMessage()]);
            $status['entity_id'] = '';

            return $status;
        }
    }

    public function isCustomerExistByInterpriseCode($is_code)
    {
        $customercollection = $this->customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("interprise_customer_code", ["eq" => $is_code])
            - load();
        if ($customercollection->count() > 0) {
            $data = $customercollection->getFirstItem();

            return $data['entity_id'];
        } else {
            return 0;
        }
    }

    public function caseExists($csse_no)
    {
        $colect = $this->casecolFactory->create();
        $colect->addFieldToFilter('case_number', ['eq' => $csse_no]);
        if ($colect->count() > 0) {
            $resault = $colect->getFirstItem();

            return $resault['entity_id'];
        } else {
            return 0;
        }
    }
}
