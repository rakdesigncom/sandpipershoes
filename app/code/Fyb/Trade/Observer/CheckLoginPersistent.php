<?php

namespace Fyb\Trade\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;

class CheckLoginPersistent implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $allowedActions = [
        'iscron_cron_scheduler',
        'iscron_cron_processor',
        'iscron_cron_reattemptcron',
        'customer_section_load',
        'loginascustomer_login_index'
    ];

    protected $appState;

    protected $urlInterface;

    protected $storeManager;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->appState = $appState;
        $this->urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeCode = $this->storeManager->getWebsite()->getCode();
        $area = $this->appState->getAreaCode();

        if ($storeCode === \Fyb\Trade\Helper\Data::TRADE_STORE_CODE && $area === 'frontend') {
            $this->_redirect($observer);
        }
    }

    protected function _redirect($observer)
    {
        $actionName = $observer->getEvent()->getRequest()->getFullActionName();
        $controller = $observer->getControllerAction();

        if (!in_array($actionName, $this->allowedActions)) {
            $path = trim($controller->getRequest()->getPathInfo(), '/');

            if (!$this->_customerSession->isLoggedIn() && strpos($path, 'customer/') === false) {
                $this->_customerSession->authenticate();

//                $loginUrl = ObjectManager::getInstance()->get(\Magento\Customer\Model\Url::class)->getLoginUrl();
//                $controller->getResponse()->setRedirect($loginUrl);
            }
        }
    }
}
