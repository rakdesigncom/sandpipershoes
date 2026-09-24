<?php


namespace Interprise\Logger\Api\Data;

interface PricelistsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TO_QTY = 'to_qty';
    const PRICE = 'price';
    const UNITOFMEASURE = 'unitofmeasure';
    const PRICELISTS_ID = 'id';
    const PRICELIST = 'pricelist';
    const ITEMCODE = 'itemcode';
    const FROM_QTY = 'from_qty';
    const CURRENCY = 'currency';

    /**
     * Get pricelists_id
     * @return string|null
     */
    public function getPricelistsId();

    /**
     * Set pricelists_id
     * @param string $pricelistsId
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPricelistsId($pricelistsId);

    /**
     * Get itemcode
     * @return string|null
     */
    public function getItemcode();

    /**
     * Set itemcode
     * @param string $itemcode
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setItemcode($itemcode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PricelistsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PricelistsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PricelistsExtensionInterface $extensionAttributes
    );

    /**
     * Get pricelist
     * @return string|null
     */
    public function getPricelist();

    /**
     * Set pricelist
     * @param string $pricelist
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPricelist($pricelist);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPrice($price);

    /**
     * Get from_qty
     * @return string|null
     */
    public function getFromQty();

    /**
     * Set from_qty
     * @param string $fromQty
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setFromQty($fromQty);

    /**
     * Get to_qty
     * @return string|null
     */
    public function getToQty();

    /**
     * Set to_qty
     * @param string $toQty
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setToQty($toQty);

    /**
     * Get currency
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set currency
     * @param string $currency
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setCurrency($currency);

    /**
     * Get unitofmeasure
     * @return string|null
     */
    public function getUnitofmeasure();

    /**
     * Set unitofmeasure
     * @param string $unitofmeasure
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setUnitofmeasure($unitofmeasure);
}
