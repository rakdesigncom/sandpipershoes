<?php
namespace Interprise\Logger\Block\Adminhtml\Diagnostics;

use Magento\Backend\Block\Template;

class Index extends Template
{
    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Interprise\Logger\Model\InstallwizardFactory $installwizard
     * @param \Interprise\Logger\Model\CronActivityScheduleFactory $activityScheduleFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Interprise\Logger\Model\InstallwizardFactory $installwizard,
        \Interprise\Logger\Model\ResourceModel\CronActivitySchedule\CollectionFactory $activityScheduleFactory
    ) {
        $this->installwizard = $installwizard;
        $this->activityschedule = $activityScheduleFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function syncDone()
    {
        $installwizard = $this->installwizard->create();
        $collection = $installwizard->getCollection();
        $collection->addFieldToFilter('sync_done', ['eq' => 0]);
        $collection->setPageSize(1)->setCurPage(1);
        if ($collection->count()>0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSyncDone($id)
    {
        $installwizard = $this->installwizard->create()->load($id);
        $installwizard->setData('sync_done', 1);
        $installwizard->setData('action', 'Done');
        $installwizard->setData('status', 'Done');
        $installwizard->save();
    }

    public function checkPreviousChangelog($master_id)
    {
        $master_id--;
        $installwizard = $this->installwizard->create()->load($master_id);
        if ($installwizard->getId()) {
            $result = $installwizard->getData();
            return $result['sync_done'];
        }
        return false;
    }

    public function getInstallwizardstatus()
    {
        $installwizard = $this->installwizard->create();
        $collection = $installwizard->getCollection();
        $collection->getSelect()->order('sort_order ASC');
        return $collection;
    }

    public function getActivityScheduleProcess($id = '', $type = '')
    {
        $arrr = [];
        $collections = $this->activityschedule->create();
        if ($id!='') {
            $collections->addFieldToFilter('CronMasterId', $id);
        }
        $collections->getSelect()
            ->columns('COUNT(cronactivityschedule_id) as total')
            ->group(['CronMasterId','DataId','Status'])
            ->order('CronMasterId DESC');
        foreach ($collections as $colnew) {
            $value = $colnew->getData();
            $arrr[$value['CronMasterId']][$value['Status']] = $value['total'];
        }
        return $arrr;
    }

    /**
     * @return bool
     */
    public function getCollection()
    {
        $installwizard = $this->installwizard->create();
        $collection = $installwizard->getCollection();
        if ($collection->count()>0) {
            return $collection;
        } else {
            return false;
        }
    }

    /**
     * @param $master_id
     * @return array|int
     */
    public function processChangeLog($master_id)
    {
        $activityschedule = $this->activityschedule->create();
        $activityschedule->addFieldToFilter('CronMasterId', ['eq' => $master_id]);
        $activityschedule->getSelect()->columns([
            'counts' => new \Zend_Db_Expr('count(Status)'),
            'Status'=>new \Zend_Db_Expr('count(Status)')])
            ->group('Status');
        $return_arr=[];
        if ($activityschedule->count()) {
            foreach ($activityschedule as $ns => $nvs) {
                $return_arr[$nvs['Status']]=$nvs['counts'];
            }
            return $return_arr;
        } else {
            return 0;
        }
    }
    /**
     * @param $masterid
     * @return bool
     */
    public function countpendingitems($masterid)
    {
        $activityschedule = $this->activityschedule->create();
        $activityschedule->addFieldToFilter('CronMasterId', ['eq' => $masterid]);
        $activityschedule->addFieldToFilter('Status', ['eq' => 'pending']);
        $activityschedule->getSelect()->columns(['totalcounts' => new \Zend_Db_Expr('count(*)')]);
        $activityschedule->setPageSize(1)->setCurPage(1);
        if ($activityschedule->count()>0) {
            $resault = $activityschedule->getFirstItem();
            return $resault['totalcounts'];
        }
        return false;
    }

    /**
     * @param $master_id
     * @return bool
     */
    public function checkPreviousChangelog1($master_id)
    {
        $master_id--;
        $installwizard = $this->installwizard->create();
        $collection = $installwizard->getCollection();
        $collection->addFieldToFilter('id', ['eq' => $master_id]);
        $collection->setPageSize(1)->setCurPage(1);
        if ($collection->count()>0) {
            $data = $collection->getFirstItem();
            return $data->getSyncDone();
        }
        return false;
    }
}
