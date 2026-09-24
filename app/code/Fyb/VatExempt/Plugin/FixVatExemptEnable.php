<?php

namespace Fyb\VatExempt\Plugin;

use Meetanshi\VatExempt\Helper\Data;
use Magento\Store\Model\ScopeInterface;

class FixVatExemptEnable
{
    /**
     * @param Data $subject
     * @param string $scope
     *
     * @return array
     */
    public function beforeIsEnabled(Data $subject, $scope = ScopeInterface::SCOPE_STORE): array
    {
        return [$scope];
    }
}
