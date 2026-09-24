<?php

namespace Fyb\Trade\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\Format;

class TradePrice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Locale\Format|mixed
     */
    protected $localeFormat;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Format $localeFormat = null,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->localeFormat = $localeFormat ?: ObjectManager::getInstance()->get(Format::class);
    }

    public function getPriceFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }
}
