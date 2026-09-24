<?php

namespace Fyb\Theme\Plugin;

use Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable;

class ConfigurableProductName
{
    /**
     * @param Configurable $subject
     * @param callable $proceed
     *
     * @return string
     */
    public function aroundGetProductName(Configurable $subject, callable $proceed): string
    {
        return $subject->getItem()->getName();
    }
}
