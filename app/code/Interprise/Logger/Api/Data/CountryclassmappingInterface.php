<?php


namespace Interprise\Logger\Api\Data;

interface CountryclassmappingInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const COUNTRYCLASSMAPPING_ID = 'id';
    const ISO_CODE = 'iso_code';

    /**
     * Get countryclassmapping_id
     * @return string|null
     */
    public function getCountryclassmappingId();

    /**
     * Set countryclassmapping_id
     * @param string $countryclassmappingId
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     */
    public function setCountryclassmappingId($countryclassmappingId);

    /**
     * Get iso_code
     * @return string|null
     */
    public function getIsoCode();

    /**
     * Set iso_code
     * @param string $isoCode
     * @return \Interprise\Logger\Api\Data\CountryclassmappingInterface
     */
    public function setIsoCode($isoCode);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Interprise\Logger\Api\Data\CountryclassmappingExtensionInterface $extensionAttributes
    );
}
