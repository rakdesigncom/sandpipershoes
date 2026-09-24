<?php

namespace Interprise\Logger\Controller\Adminhtml\FailedOrders;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Interprise\Logger\Model\ResourceModel\FailedOrders\CollectionFactory;
use DateTime;
use DatePeriod;
use DateInterval;

class MassUpdate extends \Magento\Backend\App\Action
{
    protected $_resource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Magento\Framework\App\ResourceConnection $resource,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->_resource = $resource;
        parent::__construct($context);
    }

    /**
     * Delete one or more subscribers action
     *
     * @return void
     */
    public function execute()
    {
        echo "inside controller";
        // die;
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $connection = $this->_resource->getConnection();

        foreach ($collection->getAllIds() as $id) {
            // $store->delete();
            $data = $this->getDataRequired($id);
            $this->manipulateData($data, $id);
            // echo "<pre>";
            // print_r($collection->getAllIds());
            // echo "</pre>";
            // die;

            // $tableName = $this->_resource->getTableName('interprise_logger_failedorders');
            // $sql = "Select Next_attempt,Attempt_no,Last_attempt FROM " . $tableName . " WHERE failedorder_id=".$id;
            // $data = $connection->fetchAll($sql);
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // echo $id."->NA<br>".$data[0]['Next_attempt'];
            // echo '<br>'.$id."->LA<br>".$data[0]['Last_attempt'];
            // echo '<br>'.$id."->AN<br>".$data[0]['Attempt_no'];
            // $na = new DateTime($data[0]['Next_attempt']);
            // $la = new DateTime($data[0]['Last_attempt']);
            // $an = $data[0]['Attempt_no'];
            // $diff=date_diff($na,$la);
            // $difference = $diff->format("%a");
            // die;
            // if($na == $la){
            // $minutes_to_add = 1;
            // $time = $la;
            // $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            // $Next_attempt = $time->format('Y-m-d H:i:s');
            // $sql = "Update " . $tableName . " Set Next_attempt='" .$Next_attempt. "',Attempt_no=1 WHERE failedorder_id=".$id;
            // $connection->query($sql);
            // }
        }
        $this->messageManager->addSuccess(__('%1 record(s) updated.Check status after 5 minutes', $collectionSize));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/index');
    }

    public function getDataRequired($id)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('interprise_logger_failedorders');
        $sql = "Select Next_attempt,Attempt_no,Last_attempt FROM " . $tableName . " WHERE failedorder_id=" . $id;
        $result = $connection->fetchAll($sql);

        return $result;
    }

    public function manipulateData($data, $id)
    {
        $na = new DateTime($data[0]['Next_attempt']);
        $la = new DateTime($data[0]['Last_attempt']);
        $an = $data[0]['Attempt_no'];
        $diff = date_diff($na, $la);
        $difference = $diff->format("%a");
        // die;
        // if($difference==0){
        $minutes_to_add = 1;
        $time = $la;
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $stamp = $time->format('Y-m-d H:i:s');
        $this->setDataRequired($id, $stamp);
        // }
    }

    public function setDataRequired($id, $Next_attempt)
    {
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('interprise_logger_failedorders');
        $sql = "Update " . $tableName . " Set Next_attempt='" . $Next_attempt . "',Attempt_no=1 WHERE failedorder_id=" . $id;
        // echo $sql;
        // die;
        $connection->query($sql);
    }
}
