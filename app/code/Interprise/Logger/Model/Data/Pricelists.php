<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\PricelistsInterface;

class Pricelists extends \Magento\Framework\Api\AbstractExtensibleObject implements PricelistsInterface
{

    /**
     * Get pricelists_id
     * @return string|null
     */
    public function getPricelistsId()
    {
        return $this->_get(self::PRICELISTS_ID);
    }

    /**
     * Set pricelists_id
     * @param string $pricelistsId
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPricelistsId($pricelistsId)
    {
        return $this->setData(self::PRICELISTS_ID, $pricelistsId);
    }

    /**
     * Get itemcode
     * @return string|null
     */
    public function getItemcode()
    {
        return $this->_get(self::ITEMCODE);
    }

    /**
     * Set itemcode
     * @param string $itemcode
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setItemcode($itemcode)
    {
        return $this->setData(self::ITEMCODE, $itemcode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PricelistsExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PricelistsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PricelistsExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get pricelist
     * @return string|null
     */
    public function getPricelist()
    {
        return $this->_get(self::PRICELIST);
    }

    /**
     * Set pricelist
     * @param string $pricelist
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPricelist($pricelist)
    {
        return $this->setData(self::PRICELIST, $pricelist);
    }

    /**
     * Get price
     * @return string|null
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get from_qty
     * @return string|null
     */
    public function getFromQty()
    {
        return $this->_get(self::FROM_QTY);
    }

    /**
     * Set from_qty
     * @param string $fromQty
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setFromQty($fromQty)
    {
        return $this->setData(self::FROM_QTY, $fromQty);
    }

    /**
     * Get to_qty
     * @return string|null
     */
    public function getToQty()
    {
        return $this->_get(self::TO_QTY);
    }

    /**
     * Set to_qty
     * @param string $toQty
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setToQty($toQty)
    {
        return $this->setData(self::TO_QTY, $toQty);
    }

    /**
     * Get currency
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->_get(self::CURRENCY);
    }

    /**
     * Set currency
     * @param string $currency
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get unitofmeasure
     * @return string|null
     */
    public function getUnitofmeasure()
    {
        return $this->_get(self::UNITOFMEASURE);
    }

    /**
     * Set unitofmeasure
     * @param string $unitofmeasure
     * @return \Interprise\Logger\Api\Data\PricelistsInterface
     */
    public function setUnitofmeasure($unitofmeasure)
    {
        return $this->setData(self::UNITOFMEASURE, $unitofmeasure);
    }
}
