<?php

namespace Fyb\Shipping\Model\Carrier;

use Magento\Framework\App\ObjectManager;
use Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;

class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements CarrierInterface
{
    const DROPSHIP_METHOD = 'dropship';

    const NOT_DROPSHIP_FREE_METHOD = 'free';

    /**
     * @var string
     */
    protected $_code = 'sps';

    protected $_methods = [
        'class2nd',
        'class1st',
        'wday',
        'nextpreday',
    ];

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\OfflineShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     *
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();
        $totalWeight = $this->getTotalWeight($request);

        $isDropship = $this->isDropship($request);

//        $this->addDropshipDelivery($isDropship, $result);
//        $isFreeAdded = $this->addNonDropshipDelivery($isDropship, $result);

        $isFreeAdded = false;
        $isDropShipAdded = false;
        $isBaseAdded = false;
        foreach ($this->_methods as $rateMethod) {
            if (!$this->isMethodEnabled($rateMethod, $isDropship)) {
                continue;
            }

            if ($this->isRateApplicableToWeight($totalWeight, $rateMethod)) {
                $shippingPrice = $this->getShippingPrice($request, $freeBoxes, $rateMethod);

                if (!$isDropship && !$isBaseAdded) {
                    $shippingPrice = $this->getConfigData('drop_ship_price');
                    $isBaseAdded = true;
                }

                if ($isDropship && !$isDropShipAdded) {
                    $shippingPrice = $this->getConfigData('drop_ship_price');
                    $isDropShipAdded = true;
                    $isBaseAdded = true;
                }

                if (!$isFreeAdded && !$isDropship && $this->getConfigData($rateMethod . '/can_be_free')) {
                    $shippingPrice = '0.00';
                    $isFreeAdded = true;
                    $isBaseAdded = true;
                }

                if ($shippingPrice !== false) {
                    $method = $this->createResultMethod($shippingPrice, $rateMethod);
                    $result->append($method);
                }
            }
        }

        return $result;
    }

    /**
     * Get count of free boxes
     *
     * @param RateRequest $request
     *
     * @return int
     */
    private function getFreeBoxesCount(RateRequest $request)
    {
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                $freeShippingMethod = $item->getFreeShippingMethod();

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);
                } else if (
                    $item->getFreeShipping()
                    && ($freeShippingMethod === null || $freeShippingMethod === $this->_code . '_free')
                ) {
                    $freeBoxes += $item->getQty();
                }
            }
        }

        return $freeBoxes;
    }

    /**
     * Returns free boxes count of children
     *
     * @param mixed $item
     *
     * @return mixed
     */
    private function getFreeBoxesCountFromChildren($item)
    {
        $freeBoxes = 0;
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes += $item->getQty() * $child->getQty();
            }
        }

        return $freeBoxes;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return float
     */
    protected function getTotalWeight(RateRequest $request)
    {
        return $request->getPackageWeight();
    }

    /**
     * @param RateRequest $request
     *
     * @return bool
     */
    protected function isDropship($request)
    {
        $postCheck = $_POST['order']['shipping_address']['is_dropship'] ?? false;
        if ($postCheck) {
            return $postCheck == 1;
        }
        if ($request->getAllItems()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $request->getAllItems()[0]->getQuote();

            return (bool)$quote->getData('is_dropship');
        }

        return false;
    }

    /**
     * @param bool $isDropship
     * @param \Magento\Shipping\Model\Rate\Result $result
     *
     * @return void
     */
    protected function addDropshipDelivery($isDropship, $result)
    {
        if (!$this->isMethodEnabled(self::DROPSHIP_METHOD, $isDropship)) {
            return;
        }

        if ($isDropship) {
            $shippingPrice = $this->getConfigData(self::DROPSHIP_METHOD . '/price');
            $method = $this->createResultMethod($shippingPrice, self::DROPSHIP_METHOD);
            $result->append($method);
        }
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    protected function isMethodEnabled($method, $isDropship)
    {
        $isOkWithAddress = true;
        if ($isDropship) {
            $isOkWithAddress = (bool)$this->getConfigData($method . '/enable_dropship');
        }

        return (bool)$this->getConfigData($method . '/active') && $isOkWithAddress;
    }

    /**
     * Creates result method
     *
     * @param int|float $shippingPrice
     * @param string $rateMethod
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice, $rateMethod)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData($rateMethod . '/name') );

        $method->setMethod($rateMethod);
        $method->setMethodTitle($this->getConfigData('title'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        return $method;
    }

    /**
     * @param bool $isDropship
     * @param \Magento\Shipping\Model\Rate\Result $result
     *
     * @return bool
     */
    protected function addNonDropshipDelivery($isDropship, $result)
    {
        if (!$this->isMethodEnabled(self::NOT_DROPSHIP_FREE_METHOD, $isDropship)) {
            return false;
        }

        if (!$isDropship) {
            $shippingPrice = $this->getConfigData(self::NOT_DROPSHIP_FREE_METHOD . '/price');
            $method = $this->createResultMethod($shippingPrice, self::NOT_DROPSHIP_FREE_METHOD);
            $result->append($method);

            return true;
        }

        return false;
    }

    protected function isRateApplicableToWeight($weight, $rateMethod)
    {
        $rateMinWeight = $this->getConfigData($rateMethod . '/min_weight');
        $rateMaxWeight = $this->getConfigData($rateMethod . '/max_weight');

        if ($rateMinWeight !== null && (float)$weight < (float)$rateMinWeight) {
            return false;
        }

        if ($rateMaxWeight !== null && (float)$weight > (float)$rateMaxWeight) {
            return false;
        }

        return true;
    }

    /**
     * Returns shipping price
     *
     * @param RateRequest $request
     * @param int $freeBoxes
     * @param $rateMethod
     *
     * @return bool|float
     */
    private function getShippingPrice(RateRequest $request, $freeBoxes, $rateMethod)
    {
        $shippingPrice = false;

        $configPrice = $this->getConfigData($rateMethod . '/price');
        if ($this->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);
        } else if ($this->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && $request->getPackageQty() == $freeBoxes) {
            $shippingPrice = '0.00';
        }

        return $shippingPrice;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
