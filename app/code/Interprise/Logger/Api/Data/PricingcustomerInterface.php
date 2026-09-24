<?php


namespace Interprise\Logger\Api\Data;

interface PricingcustomerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMER_CODE = 'customer_code';
    const PRICE = 'price';
    const MIN_QTY = 'min_qty';
    const QTY_UPTO = 'qty_upto';
    const ITEM_CODE = 'item_code';
    const CURRENCY = 'currency';
    const PRICINGCUSTOMER_ID = 'id';

    /**
     * Get pricingcustomer_id
     * @return string|null
     */
    public function getPricingcustomerId();

    /**
     * Set pricingcustomer_id
     * @param string $pricingcustomerId
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setPricingcustomerId($pricingcustomerId);

    /**
     * Get item_code
     * @return string|null
     */
    public function getItemCode();

    /**
     * Set item_code
     * @param string $itemCode
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setItemCode($itemCode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface $extensionAttributes
    );

    /**
     * Get customer_code
     * @return string|null
     */
    public function getCustomerCode();

    /**
     * Set customer_code
     * @param string $customerCode
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setCustomerCode($customerCode);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setPrice($price);

    /**
     * Get min_qty
     * @return string|null
     */
    public function getMinQty();

    /**
     * Set min_qty
     * @param string $minQty
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setMinQty($minQty);

    /**
     * Get qty_upto
     * @return string|null
     */
    public function getQtyUpto();

    /**
     * Set qty_upto
     * @param string $qtyUpto
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setQtyUpto($qtyUpto);

    /**
     * Get currency
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set currency
     * @param string $currency
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setCurrency($currency);
}
