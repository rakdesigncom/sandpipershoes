<?php


namespace Interprise\Logger\Model\Data;

use Interprise\Logger\Api\Data\CountryclassmappingInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class Countryclassmapping extends AbstractExtensibleObject implements CountryclassmappingInterface
{
    /**
     * Get countryclassmapping_id
     * @return string|null
     */
    public function getCountryclassmappingId()
    {
        return $this->_get(self::COUNTRYCLASSMAPPING_ID);
    }

    /**
     * Set countryclassmapping_id
     * @param string $countryclassmappingId
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     */
    public function setCountryclassmappingId($countryclassmappingId)
    {
        return $this->setData(self::COUNTRYCLASSMAPPING_ID, $countryclassmappingId);
    }

    /**
     * Get iso_code
     * @return string|null
     */
    public function getIsoCode()
    {
        return $this->_get(self::ISO_CODE);
    }

    /**
     * Set iso_code
     * @param string $isoCode
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     */
    public function setIsoCode($isoCode)
    {
        return $this->setData(self::ISO_CODE, $isoCode);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
