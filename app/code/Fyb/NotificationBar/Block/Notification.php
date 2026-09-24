<?php
/**
 * @author FYB Romania
 * @copyright Copyright (c) FYB Romania (https://fyb.ro)
 * @package Notification Bar for Magento 2
 */

namespace Fyb\NotificationBar\Block;

use Laminas\Uri\Http as HttpUri;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Notification extends Template
{
    /**
     * @param string $path
     * @param string $configType
     *
     * @return mixed
     */
    public function getConfig(string $path, $configType = 'general')
    {
        return $this->_scopeConfig->getValue(
            'notification_bar/' . $configType . '/' . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function needDisplay()
    {
        $dateFrom = $this->getConfig('from_date');
        $dateTo = $this->getConfig('to_date');

        /** @var HttpUri $page */
        $page = $this->getRequest()->getUri();
        $isExcludedPage = false;
        if ($page && trim($page->getPath(), '/')) {
            $uri = trim($page->getPath(), '/');
            $excludedUrls = array_map('trim',
                array_filter(explode(',', $this->getConfig('pages_exclude')))
            );

            foreach ($excludedUrls as $excludedUrl) {
                $excludedUrl = str_replace('/', '\/', trim($excludedUrl, '/'));
                if ($excludedUrl && preg_match('/' . $excludedUrl . '/i', $uri)) {
                    $isExcludedPage = true;
                    break;
                }
            }
        }

        return $this->getConfig('enabled') && !$isExcludedPage
            && (!$dateFrom || time() >= strtotime($dateFrom))
            && (!$dateTo || time() <= strtotime($dateTo));
    }
}
