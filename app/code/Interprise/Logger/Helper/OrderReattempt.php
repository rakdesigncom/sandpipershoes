<?php

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of Pushsalesorder
 *
 * @author shadab
 */
class OrderReattempt extends Data
{
    const XML_PATH_TRANSACTION_SUCCESS_MAIL = 'setup/email_settings/reattempt_success';

    const XML_PATH_TRANSACTION_FAILURE_MAIL = 'setup/email_settings/reattempt_failure';

    const XML_PATH_TRANSACTION_SYNC_FAILED = 'setup/email_settings/sync_failed';

    const XML_PATH_TRANSACTION_SYNC_SUCCEED = 'setup/email_settings/sync_succeed';

    public $order;

    public $_customer;

    public $_pushcustomer;

    public $_pushcustomeraddress;

    public $_product;

    public $warehousecodefulfillment;

    public $magentoCustomerId;

    public $connection;

    public $resource;

    public $_failedordersctory;

    public $_reattemptFrequencyFactory;

    protected $_template;

    protected $_transportBuilder;

    protected $inlineTranslation;

    protected $changelog;

    protected $storeManager;

    protected $_escaper;

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
        \Interprise\Logger\Model\FailedOrdersFactory $failedOrdersFactory,
        \Interprise\Logger\Model\ReattemptFrequencyFactory $reattemptFrequencyFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Magento\Framework\App\ResourceConnection $resourceCon
    ) {
        $this->_product = $product;
        $this->resource = $resourceCon;
        $this->connection = $this->resource->getConnection();
        $this->_failedordersctory = $failedOrdersFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->changelog = $changelogFactory;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_reattemptFrequencyFactory = $reattemptFrequencyFactory;
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

    public function updateFailedOrdersData($outPut, $data)
    {
        // echo '<pre>';
        // print_r($outPut);
        // print_r($data);
        // echo '</pre>';
        echo '<br/>' . $data_id = $data['DataId'];
        if (strtolower($outPut['Status']) == 'success') {
            $failedOrdersResult = $this->checkFailedOrdersByDataId($data_id);
            if ($failedOrdersResult) {
                $deletedRecord = $this->deleteFailedOrderByDataId($data_id);
                if ($deletedRecord) {
                    ///////////Send email to admin /////////////////////
                    $content['send_to_email'] = ['sales@sandpipershoes.co.uk'];
                    $content['send_to_name'] = 'Interprise';

                    $content['order_id'] = $outPut['IncrementId'];
                    $content['reason'] = $outPut['Remarks'];
                    $content['json'] = $data['JsonData'];
                    $content['cronactivityschedule_id'] = $data['cronactivityschedule_id'];
                    //$content['contents']=rtrim($produc_html,",\r\n\r\n&nbsp;&nbsp;");

                    $this->sendmessage(self::XML_PATH_TRANSACTION_SUCCESS_MAIL, $content);
                }
            }
            // echo '<pre>failedOrdersResult';
            // print_r($failedOrdersResult);
            // echo '</pre>';
        } else if (strtolower($outPut['Status']) == 'fail') {
            $failedOrdersResult = $this->checkFailedOrdersByDataId($data_id);
            if ($failedOrdersResult) {
                //If record found in interprise_logger_failedorders ///////////////
                $interVal = $this->getReattemptInterval($failedOrdersResult['Attempt_no']);
                $updateRecord = $this->updateFailedOrderByDataId($data_id, $interVal, $outPut);

                return $updateRecord;
            } else {
                //If record not found in interprise_logger_failedorders ///////////////
                $insertedRecord = $this->insertFailedOrderRecord($outPut, $data);
                $content['send_to_email'] = ['sales@sandpipershoes.co.uk'];
                $content['send_to_name'] = 'Interprise';
                $content['order_id'] = $outPut['IncrementId'];
                $content['reason'] = $outPut['Remarks'];
                $content['json'] = $data['JsonData'];
                $content['cronactivityschedule_id'] = $data['cronactivityschedule_id'];
                $this->sendmessage(self::XML_PATH_TRANSACTION_FAILURE_MAIL, $content);
                ///////////Send email to admin /////////////////////
            }
        }
    }

    public function checkFailedOrdersByDataId($data_id)
    {
        $collections = $this->_failedordersctory->create()->getCollection()
            ->addFieldToFilter('Changelog_item_id', ['eq' => "$data_id"])
            ->setOrder('failedorder_id', 'DESC')
            ->setPageSize(1)->setCurPage(1);

        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();

            return $result;
        }

        return false;
    }

    public function deleteFailedOrderByDataId($data_id)
    {
        if ($data_id != '') {
            try {
                //$this->connection = $resource->getConnection();
                $whereConditions = [];
                $whereConditions[] = 'Changelog_item_id = ' . $data_id;
                $this->connection->delete('interprise_logger_failedorders', $whereConditions);

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function sendmessage($templateId, $content)
    {
        //$to_name = Mage::getStoreConfig('trans_email/ident_general/name');
        //$to_mail = Mage::getStoreConfig('trans_email/ident_general/email');
        $to_name = "Sandpipershoes";
        $to_mail = 'sales@sandpipershoes.co.uk';

        ///////////////////////////////
        $storeId = $this->storeManager->getStore()->getId();
        $senderInfo = ['email' => $to_mail, 'name' => $to_name];
        // echo '<pre>';
        // print_r($content);
        // echo '</pre>';
        $receiverInfo = ['email' => "sales@sandpipershoes.co.uk", 'name' => 'Interprise'];
        if (isset($content['CustomerCode'])) {
            $emailTempVariables = [
                'store' => $this->storeManager->getStore(),
                'customer_name' => 'Manisha',
                'message' => 'Hello World!!.',
                'EmailID' => $content['EmailID'],
                'CustomerCode' => $content['CustomerCode'],
                'Msg' => $content['Msg'],
            ];
        } else {
            $emailTempVariables = [
                'store' => $this->storeManager->getStore(),
                'customer_name' => 'Interprise',
                'message' => 'Hello World!!.',
                'order_id' => $content['order_id'],
                'reason' => $content['reason'],
                'json' => $content['json'],
                'cronactivityschedule_id' => $content['cronactivityschedule_id'],
            ];
        }

        echo '<br/>$this->_template' . $this->_template = $this->getTemplateId($templateId);
        //$this->_template = $templateId;
        $this->inlineTranslation->suspend();
        $this->generateTemplate(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo,
            $storeId
        );
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            echo '<br/>Error ' . $e->getMessage();
        }
        $this->inlineTranslation->resume();
    }

    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    protected function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $storeId)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $template = $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name'])
            ->addCc('mircea@fyb.ro');

        return $this;
    }

    public function getReattemptInterval($attemptNo)
    {
        $collections = $this->_reattemptFrequencyFactory->create()->getCollection()
            ->addFieldToFilter('Attempt_no', ['eq' => "$attemptNo"])
            ->setOrder('reattempt_id', 'DESC')
            ->setPageSize(1)->setCurPage(1);

        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();

            return $result['Interval'];
        }

        return false;
    }

    public function updateFailedOrderByDataId($data_id, $interVal, $outPut)
    {
        $collections = $this->_failedordersctory->create()->getCollection()
            ->addFieldToFilter('Changelog_item_id', ['eq' => "$data_id"])
            ->setOrder('failedorder_id', 'DESC')
            ->setPageSize(1)->setCurPage(1);

        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();
        }

        if (isset($result['Changelog_item_id'])) {
            // $collections_update = $this->_failedordersctory->create()->getCollection()
            //     ->addFieldToFilter('Changelog_item_id', ['eq' => $result['Changelog_item_id']])
            //     ->addFieldToFilter('failedorder_id', ['eq' => $result['failedorder_id']]);
            // if ($collections_update->count()>0) {
            //     foreach ($collections_update as $item_update) {
            //         $newAttempt_no = $result['Attempt_no'] + 1;
            //         $nextAttempt = date('Y-m-d H:i:s',strtotime($result['Next_attempt']) + $interVal);
            //         $collections_update->setData('Attempt_no', $newAttempt_no);
            //         $collections_update->setData('Status', 'Fail');
            //         $collections_update->setData('Next_attempt', $nextAttempt);
            //     }
            //     $collections_update->save();
            // }

            $newAttempt_no = $result['Attempt_no'] + 1;
            $nextAttempt = date('Y-m-d H:i:s', strtotime($result['Next_attempt']) + $interVal);
            $modelFailedOrder = $this->_failedordersctory->create()->load($result['failedorder_id']);
            $modelFailedOrder->setData('Reason', $outPut['Remarks']);
            $modelFailedOrder->setData('Attempt_no', $newAttempt_no);
            $modelFailedOrder->setData('Status', 'Fail');
            $modelFailedOrder->setData('Next_attempt', $nextAttempt);
            $modelFailedOrder->save();

            return true;
        } else {
            return false;
        }

        return false;
    }

    public function insertFailedOrderRecord($outPut, $data)
    {
        if (!empty($data)) {
            $changelogId = $this->getChangelogIdByItemId($data['DataId']);
            $interVal = $this->getReattemptInterval(1);
            $lastAttempt = $this->getCurrentTime();
            $nextAttempt = date('Y-m-d H:i:s', strtotime($lastAttempt) + $interVal);
            $model = $this->_failedordersctory->create();
            $model->setData('Increment_id', $outPut['IncrementId']);
            $model->setData('Changelog_item_id', $data['DataId']);
            $model->setData('Changelog_id', $changelogId);
            $model->setData('Reason', $outPut['Remarks']);
            $model->setData('Status', 'Fail');
            $model->setData('Last_attempt', $lastAttempt);
            $model->setData('Attempt_no', 1);
            $model->setData('Next_attempt', $nextAttempt);
            $model->save();

            return true;
        }

        return false;
    }

    public function getChangelogIdByItemId($data_id)
    {
        $collections = $this->changelog->create()->getCollection()
            ->addFieldToFilter('ItemId', ['eq' => "$data_id"])
            ->addFieldToFilter('ItemType', ['eq' => "order"])
            ->setPageSize(1)->setCurPage(1);

        if ($collections->count() > 0) {
            $result = $collections->getFirstItem();
            $result = $result->getData();

            return $result['changelog_id'];
        }

        return false;
    }

    public function sendServerMsg($templateId, $content)
    {
        //$to_name = Mage::getStoreConfig('trans_email/ident_general/name');
        //$to_mail = Mage::getStoreConfig('trans_email/ident_general/email');
        // echo "Inside sendServerMsg";
        $to_name = "Sandpipershoes";
        $to_mail = 'sales@sandpipershoes.co.uk';

        ///////////////////////////////
        $storeId = $this->storeManager->getStore()->getId();
        $senderInfo = ['email' => $to_mail, 'name' => $to_name];
        echo '<pre>';
        print_r($senderInfo);
        echo '</pre>';
        $receiverInfo = ['email' => "sales@sandpipershoes.co.uk", 'name' => 'Interprise'];
        $emailTempVariables = [
            'store' => $this->storeManager->getStore(),
            'customer_name' => 'Manisha',
            'message' => 'Hello World!!.',
            'CronName' => $content['CronName'],
        ];

        $this->_template = $this->getTemplateId($templateId);
        //$this->_template = $templateId;
        $this->inlineTranslation->suspend();
        $this->generateTemplate(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo,
            $storeId
        );
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            echo '<br/>Error ' . $e->getMessage();
        }
        $this->inlineTranslation->resume();
    }
}
