<?php

namespace Interprise\Logger\Cron;

class InterpriseCron
{

    protected $logger;

    protected $_scheduler;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Interprise\Logger\Model\Cron\Scheduler $scheduler
    ) {
        $this->logger = $logger;
        $this->_scheduler = $scheduler;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
//    public function execute()
//    {
//        $this->logger->addInfo("Cronjob InterpriseCron is executed.");
//    }
//    public function processor()
//    {
//        $this->_processor->execute();
//        $this->logger->addInfo("Cronprocessor for InterpriseCron is executed.");
//    }
    public function scheduler()
    {
        $this->logger->info("Cronscheduler for InterpriseCron started.");
        $this->_scheduler->execute();
        $this->logger->info("Cronscheduler for InterpriseCron stopped.");
    }
}
