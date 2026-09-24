<?php

namespace Interprise\Logger\Controller\Cronscheduler;

class Index extends \Magento\Framework\App\Action\Action
{

    // protected $resultPageFactory;

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

    protected $changelog;

    protected $cronactivityschedule;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Interprise\Logger\Helper\Pushsalesorder $pushsalesorder,
        \Interprise\Logger\Helper\Pushcrm $pushcrm,
        \Interprise\Logger\Helper\PushcustomerFactory $pushcustomerFactory,
        \Interprise\Logger\Model\CronMasterFactory $cronMasterFactory,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Interprise\Logger\Model\CronActivityScheduleFactory $cronActivityScheduleFactory
    ) {
        // $this->resultPageFactory = $resultPageFactory;
        $this->cronmasters = $cronMasterFactory;
        $this->changelog = $changelogFactory;
        $this->cronactivityschedule = $cronActivityScheduleFactory;
        $this->pushcustomer = $pushcustomerFactory;
        $this->_pushsalesorder = $pushsalesorder;
        $this->_pushcrm = $pushcrm;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $GET_CRON_RESULT = $this->cronmasters->create()->getCollection();
        $GET_CRON_RESULT->addFieldToFilter('cron_active', ['eq' => 1]);
        if (count($GET_CRON_RESULT) > 0) {
            foreach ($GET_CRON_RESULT as $key => $_cron) {
                $this->proccessChangelog($_cron);
            }
        }
    }

    public function proccessChangelog($data)
    {
        $cron_master_id = 17;
        $cron_log_id = 99;
        switch ($cron_master_id) {
            case 26:
                $GET_CRON_RESULT = $this->changelog->create()->getCollection();
                $GET_CRON_RESULT->addFieldToFilter('ItemType', ['eq' => 'customer']);
                $GET_CRON_RESULT->addFieldToFilter('Action', ['eq' => 'POST']);
                $GET_CRON_RESULT->addFieldToFilter('PushedStatus', ['eq' => 0]);
                if ($GET_CRON_RESULT->count() > 0) {
                    foreach ($GET_CRON_RESULT as $key => $_cron) {
                        $result = $this->cronactivityschedule->create();
                        $result->setData('CronLogId', $cron_log_id);
                        $result->setData('CronMasterId', 26);
                        $result->setData('ActionType', 'POST');
                        $result->setData('DataId', $_cron->getData('ItemId'));
                        $result->setData('Status', 'pending');
                        $result->save();
                    }
                }
                break;
            default:
                break;
        }
    }
}
