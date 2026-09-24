<?php

namespace MagicToolbox\Magic360\Block\Adminhtml\Settings;

/**
 * Module version block
 *
 */
class Version extends \Magento\Framework\View\Element\Template
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Data helper
     *
     * @var \MagicToolbox\Magic360\Helper\Data
     */
    protected $dataHelper = null;

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->dataHelper = $this->objectManager->get(\MagicToolbox\Magic360\Helper\Data::class);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $version = $this->dataHelper->getModuleVersion('MagicToolbox_Magic360');
        $version = $version ? $version : '';

        return $version;
    }

    /**
     * Get upgrade message
     *
     * @return string
     */
    public function getUpgradeMessage()
    {
        $requiredVersions = [
            'MagicToolbox_Sirv' => '3.2.2',
            'MagicToolbox_MagicZoomPlus' => '1.6.8',
            'MagicToolbox_MagicZoom' => '1.6.8',
            'MagicToolbox_MagicThumb' => '1.6.8',
            'MagicToolbox_MagicScroll' => '1.6.8',
            'MagicToolbox_MagicSlideshow' => '1.6.8',
        ];
        $modulesData = $this->dataHelper->getModulesData();

        $messages = [];
        foreach ($requiredVersions as $module => $requiredVersion) {
            if (isset($modulesData[$module]) && $modulesData[$module]) {
                if (version_compare($modulesData[$module], $requiredVersion, '<')) {
                    $messages[] = 'Notice: you have installed ' . $module . ' module by version ' . $modulesData[$module] . '.' .
                        ' Please, update it at least to version ' . $requiredVersion;
                }
            }
        }

        return implode('<br/>', $messages);
    }
}
