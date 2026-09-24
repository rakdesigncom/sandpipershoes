<?php

namespace Interprise\Logger\Model\Cron;

class Scheduler
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context ,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public $cronmasters;

    public $pushcustomer;

    public $_pushsalesorder;

    public $_pushcrm;

    public $_helper;

    public $_processor_active;

    public $_processor_active_order;

    protected $changelog;

    protected $cronlog;

    protected $cronactivityschedule;

    protected $casefact;

    protected $processorcontroller;

    protected $reattemptorder;

    public function __construct(
        \Interprise\Logger\Helper\Data $helper,
        \Interprise\Logger\Helper\Pushsalesorder $pushsalesorder,
        \Interprise\Logger\Helper\Pushcrm $pushcrm,
        \Interprise\Logger\Helper\PushcustomerFactory $pushcustomerFactory,
        \Interprise\Logger\Controller\Cron\Processor $processorcontroller,
        \Interprise\Logger\Model\CronMasterFactory $cronMasterFactory,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Interprise\Logger\Model\CronLogFactory $cronlogFactory,
        \Interprise\Logger\Model\CronActivityScheduleFactory $cronActivityScheduleFactory,
        \Interprise\Logger\Helper\OrderReattempt $_orderReattempt,
        \Interprise\Logger\Model\CasesFactory $caseFactory
    ) {
        $this->_helper = $helper;
        $this->pushcustomer = $pushcustomerFactory;
        $this->_pushsalesorder = $pushsalesorder;
        $this->_pushcrm = $pushcrm;
        $this->cronmasters = $cronMasterFactory;
        $this->changelog = $changelogFactory;
        $this->cronlog = $cronlogFactory;
        $this->cronactivityschedule = $cronActivityScheduleFactory;
        $this->casefact = $caseFactory;
        $this->processorcontroller = $processorcontroller;
        $this->reattemptorder = $_orderReattempt;
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
        $GET_CRON_RESULT = $this->cronmasters->create()->getCollection();
        $GET_CRON_RESULT->addFieldToFilter('cron_active', ['cron_status' => 1]);
        if ($GET_CRON_RESULT->count() > 0) {
            foreach ($GET_CRON_RESULT as $key => $_crons) {
//                break;
                $_cron = $_crons->getData();
                $timemagento = $this->_helper->getCurrentTime();
                //echo $timemagento;
                $fromtimetable = $_cron['cron_from_date'];
                //echo "<br>sdfdsfsdfsd ".$fromtimetable."<br>";
                $frequency = $_cron['cron_frequency'];
                $strtotime_now = strtotime($timemagento);
                $strtotime_from = strtotime($fromtimetable);
                $strtotime_frequency = $frequency;
                $master_id = $_cron['master_id'];
                if ($strtotime_now > $strtotime_from + $strtotime_frequency) {
                    $this->proccessChangelog($_cron);
                }
            }
        }

//        die("DSA");
//        if ($this->_processor_active_order) {
            //call processor function
            $this->processorcontroller->executeCallBackOrder();
//        }

        //if ($this->_processor_active) {
////            call processor function
            $this->processorcontroller->executeCallBack();
        //}
    }

    public function proccessChangelog($data)
    {
        echo '<br/>' . $master_id = $data['master_id'];

        $timemagento = $this->_helper->getCurrentTime();
        switch ($master_id) {
            case '1':
            case '2':
            case '3':
            case '5':
            case '7':
            case '8':
            case '11':
            case '12':
            case '13':
            case '16':
            case '18':
            case '19':
            case '22':
            case '28':
            case '31':
            case '32':
            case '33':
            case '34':
            case '35':
            case '36':
            case '37':
            case '38':
                echo '<br/>' . $endpoint = $data['cron_changelog_endpoint'];
                //interprise_logger_cronmaster
                //$this->updatefromtime($master_id);

                $from_date = $data['cron_from_date'];
                $adjust = "+2 hour";
                $timemagento = date('Y-m-d H:i:s', strtotime($adjust));
                $from_date = date('Y-m-d H:i:s', strtotime('+1 seconds', strtotime($from_date)));
                $to_date = "2030-01-01";
                print_r([$endpoint, $from_date, $to_date]);
                $change_log_result = $this->_helper->getChangeLog($endpoint, $from_date, $to_date);

                if ($change_log_result['api_error']) {
                    $result_data = $change_log_result['results']['data'];
                    $total_count = count($result_data);

                    $lastrecordtime = $result_data[$total_count - 1]['attributes']['dateCreated'];

                    $request = $change_log_result['request'];
                    $remarks = "changelog/" . $endpoint . "?from=$from_date" . "&to=$to_date";
                    $lastCronLogStatus = $this->getLastCronLogStatus($master_id);

                    if (strtolower($lastCronLogStatus) != 'success') {
                        $content['MasterId'] = $master_id;
                        $content['From'] = $from_date;
                        $content['CronName'] = $data['cron_name'];
                        $content['Records'] = $total_count;
                        $this->reattemptorder->sendServerMsg(10, $content);
                    }
                    $cron_change_log_id = $this->updateCronLog(
                        $master_id,
                        $timemagento,
                        $request,
                        $total_count,
                        $remarks,
                        "Success"
                    );
                    $priority = $this->getPrioritySchedule($total_count);
                    if ($cron_change_log_id) {
                        $this->updateCronActivitySchedule($result_data, $cron_change_log_id, $master_id, $priority);
                    }
                    if ($total_count > 0) {
                        $this->_processor_active = true;
                    }
                    $this->updatefromtime(date("Y-m-d H:i:s", strtotime($lastrecordtime)), $master_id);
                } else {
                    $total_count = 0;
                    $request = $change_log_result['request'];
                    $run_time = $timemagento;
                    $remarks = "changelog/" . $endpoint . "?from=$from_date" . "&to=$to_date";
                    $lastCronLogStatus = $this->getLastCronLogStatus($master_id);

                    $errorMsg = json_decode($change_log_result['results'], true);
                    //print_r($errorMsg);

                    if (is_array($errorMsg)) {
                        if (strtolower($lastCronLogStatus) != 'fail' && array_key_exists("error", $errorMsg[0])) {
                            $content['MasterId'] = $master_id;
                            $content['From'] = $from_date;
                            $content['CronName'] = $data['cron_name'];
                            $content['Records'] = 0;
                            $this->reattemptorder->sendServerMsg(9, $content);
                        }
                        if (array_key_exists("errors", $errorMsg[0])) {
                            $newStatus = 'Success';
                        } else {
                            $newStatus = 'Fail';
                        }
                    } else {
                        $newStatus = 'Fail';
                    }

                    if ($newStatus == 'Success' && strtolower($lastCronLogStatus) == 'fail') {
                        $content['MasterId'] = $master_id;
                        $content['From'] = $from_date;
                        $content['CronName'] = $data['cron_name'];
                        $content['Records'] = 0;
                        $this->reattemptorder->sendServerMsg(10, $content);
                    } else if ($newStatus == 'Fail' && strtolower($lastCronLogStatus) == 'success') {
                        $content['MasterId'] = $master_id;
                        $content['From'] = $from_date;
                        $content['CronName'] = $data['cron_name'];
                        $content['Records'] = 0;
                        $this->reattemptorder->sendServerMsg(9, $content);
                    }

                    $this->updateCronLog($master_id, $run_time, $request, $total_count, $remarks, $newStatus);
                }

                break;
            case 4:
                $GET_CRON_RESULT = $this->changelog->create()->getCollection();
                $GET_CRON_RESULT->addFieldToFilter('ItemType', ['eq' => 'order']);
                $GET_CRON_RESULT->addFieldToFilter('Action', ['eq' => 'POST']);
                $GET_CRON_RESULT->addFieldToFilter('PushedStatus', ['eq' => 0]);

                if ($GET_CRON_RESULT->count() > 0) {
                    $total_count = $GET_CRON_RESULT->count();
                } else {
                    $total_count = 0;
                }
                $remarks = "Total $total_count orders have to pushed";

                $cron_change_log_id = $this->updateCronLog(
                    $master_id,
                    $timemagento,
                    'GET Data from interprise_changelog table',
                    $total_count,
                    $remarks
                );
                if ($GET_CRON_RESULT) {
                    $this->_pushsalesorder->updateCronActivitySchedule($GET_CRON_RESULT, $cron_change_log_id);
                }
                if ($total_count > 0) {
                    $this->_processor_active_order = true;
                }
                break;
            case 17:
                $GET_CRON_RESULT = $this->casefact->create()->getCollection();
                $GET_CRON_RESULT->addFieldToFilter('from_created', ['eq' => 0]);
                $total_count = $GET_CRON_RESULT->count();
                $remarks = "Total $total_count case have to pushed";
                $cron_change_log_id = $this->updateCronLog(
                    $master_id,
                    $timemagento,
                    'GET Data from interprise_case table',
                    $total_count,
                    $remarks
                );
                if ($GET_CRON_RESULT) {
                    $this->_pushcrm->updateCronActivitySchedule($GET_CRON_RESULT, $cron_change_log_id);
                }
                if ($total_count > 0) {
                    $this->_processor_active = true;
                }
                break;

            case 21:
                $GET_CRON_RESULT = $this->changelog->create()->getCollection();
                $GET_CRON_RESULT->addFieldToFilter('ItemType', ['eq' => 'address']);
                $GET_CRON_RESULT->addFieldToFilter('Action', ['eq' => 'POST']);
                $GET_CRON_RESULT->addFieldToFilter('PushedStatus', ['eq' => 0]);
                $total_count = $GET_CRON_RESULT->count();
                $remarks = "Total $total_count address(es) to be updated/inserted";
                $cron_change_log_id = $this->updateCronLog(
                    $master_id,
                    $timemagento,
                    'GET Data from interprise_changelog table',
                    $total_count,
                    $remarks
                );
                if ($GET_CRON_RESULT->count() > 0) {
                    foreach ($GET_CRON_RESULT as $key => $_crons) {
                        $_cron = $_crons->getData();
                        $model = $this->cronactivityschedule->create();
                        $model->setData('CronLogId', $cron_change_log_id);
                        $model->setData('CronMasterId', $master_id);
                        $model->setData('ActionType', 'POST');
                        $model->setData('DataId', $_cron['ItemId']);
                        $model->setData('Status', 'pending');
                        $model->setData('ActivityTime', $timemagento);
                        $model->setData('Remarks', 'pending');
                        $model->setData('priority', 1);
                        $model->save();

                        $_cron_log_id = $_cron['changelog_id'];
                        $model_changelog = $this->changelog->create()->load($_cron_log_id);
                        $model_changelog->setData('PushedStatus', 1);
                        $model_changelog->save();
                    }
                    $this->_processor_active = true;
                }
                break;
            case 26:
                $GET_CRON_RESULT = $this->changelog->create()->getCollection();
                $GET_CRON_RESULT->addFieldToFilter('ItemType', ['eq' => 'customer']);
                $GET_CRON_RESULT->addFieldToFilter('Action', ['eq' => 'POST']);
                $GET_CRON_RESULT->addFieldToFilter('PushedStatus', ['eq' => 0]);

                $total_count = $GET_CRON_RESULT->count();
                $remarks = "Total $total_count Customer have to pushed";
                $cron_change_log_id = $this->updateCronLog(
                    $master_id,
                    $timemagento,
                    'GET Data from interprise_changelog table',
                    $total_count,
                    $remarks
                );
                if (count($GET_CRON_RESULT) > 0) {
                    foreach ($GET_CRON_RESULT as $key => $_crons) {
                        $_cron = $_crons->getData();
                        $model = $this->cronactivityschedule->create();
                        $model->setData('CronLogId', $cron_change_log_id);
                        $model->setData('CronMasterId', $master_id);
                        $model->setData('ActionType', 'POST');
                        $model->setData('DataId', $_cron['ItemId']);
                        $model->setData('Status', 'pending');
                        $model->setData('ActivityTime', $timemagento);
                        $model->setData('Remarks', 'pending');
                        $model->setData('priority', 1);
                        $model->save();

                        $_cron_log_id = $_cron['changelog_id'];
                        $model_changelog = $this->changelog->create()->load($_cron_log_id);
                        $model_changelog->setData('PushedStatus', 1);
                        $model_changelog->save();
                    }
                    $this->_processor_active = true;
                }
                break;

            default:
                break;
        }

        return 0;
    }

    public function getLastCronLogStatus($master_id)
    {
        $getCronLogResult = $this->cronlog->create()->getCollection();
        $getCronLogResult->addFieldToFilter('CronMasterId', ['eq' => $master_id]);
        $getCronLogResult->setOrder('cronlog_id', 'DESC');
        $getCronLogResult->setPageSize(1)->setCurPage(1);
        if ($getCronLogResult->count() > 0) {
            $result = $getCronLogResult->getFirstItem();
            $result = $result->getData();

            return $result['Status'];
        }

        return false;
    }

    public function updateCronLog($master_id, $timemagento, $request, $total_count, $remarks, $status = 'Success')
    {
        $model = $this->cronlog->create();
        $model->setData('CronMasterId', $master_id);
        $model->setData('RunTime', $timemagento);
        $model->setData('Request', $request);
        $model->setData('ActivityCount', $total_count);
        $model->setData('Remarks', $remarks);
        $model->setData('Status', $status);
        $model->save();

        return $model->getId();
    }

    public function getPrioritySchedule($total_count)
    {
        if ($total_count < 10) {
            $priority = 1;
        } else if ($total_count > 10 && $total_count < 100) {
            $priority = 2;
        } else if ($total_count >= 100 && $total_count < 500) {
            $priority = 3;
        } else {
            $priority = 4;
        }

        return $priority;
    }

    public function updateCronActivitySchedule($get_result_data, $cron_change_log_id, $master_id, $priority = 1)
    {
//        echo '<pre>$get_result_data';
//        print_r($get_result_data);
        if (count($get_result_data) > 0) {
            //echo "Inside update";
            foreach ($get_result_data as $_itmes) {
                $attribute = $_itmes['attributes'];
                $model = $this->cronactivityschedule->create();
                $model->setData('CronLogId', $cron_change_log_id);
                $model->setData('CronMasterId', $master_id);
                $model->setData('ActionType', $attribute['action']);
                $model->setData('DataId', $attribute['data']);
                $model->setData('JsonData', json_encode($_itmes));
                $model->setData('Status', 'pending');
                $model->setData('ActivityTime', $this->_helper->getCurrentTime());
                $model->setData('Remarks', 'pending');
                $model->setData('priority', $priority);
                $model->save();
            }
        }
    }

    public function updatefromtime($time, $master_id)
    {
        //$timemagento=$this->_helper->getCurrentTime();

        $model = $this->cronmasters->create()->load($master_id, 'master_id');
        $model->setData('cron_from_date', $time);
        $model->save();
    }
}

