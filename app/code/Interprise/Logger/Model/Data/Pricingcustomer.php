<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\PricingcustomerInterface;

class Pricingcustomer extends \Magento\Framework\Api\AbstractExtensibleObject implements PricingcustomerInterface
{

    /**
     * Get pricingcustomer_id
     * @return string|null
     */
    public function getPricingcustomerId()
    {
        return $this->_get(self::PRICINGCUSTOMER_ID);
    }

    /**
     * Set pricingcustomer_id
     * @param string $pricingcustomerId
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setPricingcustomerId($pricingcustomerId)
    {
        return $this->setData(self::PRICINGCUSTOMER_ID, $pricingcustomerId);
    }

    /**
     * Get item_code
     * @return string|null
     */
    public function getItemCode()
    {
        return $this->_get(self::ITEM_CODE);
    }

    /**
     * Set item_code
     * @param string $itemCode
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setItemCode($itemCode)
    {
        return $this->setData(self::ITEM_CODE, $itemCode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\PricingcustomerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get customer_code
     * @return string|null
     */
    public function getCustomerCode()
    {
        return $this->_get(self::CUSTOMER_CODE);
    }

    /**
     * Set customer_code
     * @param string $customerCode
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setCustomerCode($customerCode)
    {
        return $this->setData(self::CUSTOMER_CODE, $customerCode);
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
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get min_qty
     * @return string|null
     */
    public function getMinQty()
    {
        return $this->_get(self::MIN_QTY);
    }

    /**
     * Set min_qty
     * @param string $minQty
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setMinQty($minQty)
    {
        return $this->setData(self::MIN_QTY, $minQty);
    }

    /**
     * Get qty_upto
     * @return string|null
     */
    public function getQtyUpto()
    {
        return $this->_get(self::QTY_UPTO);
    }

    /**
     * Set qty_upto
     * @param string $qtyUpto
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setQtyUpto($qtyUpto)
    {
        return $this->setData(self::QTY_UPTO, $qtyUpto);
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
     * @return \Interprise\Logger\Api\Data\PricingcustomerInterface
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }
}
