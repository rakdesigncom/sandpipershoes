<?php

namespace Fyb\StockistLocator\Model;

class StockistPage extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'stockist_page';

    /**
     * @var \Fyb\StockistLocator\Model\ImageUploader
     */
    protected $imageUploader;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Fyb\StockistLocator\Model\ImageUploader $imageUploader,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->imageUploader = $imageUploader;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Fyb\StockistLocator\Model\ResourceModel\StockistPage::class);
    }

    public function beforeSave()
    {
        $urlKey = $this->getUrlKey();
        if (!$urlKey) {
            $urlKey =  preg_replace('/[^a-z]/i', '-', $this->getName());
            $urlKey = preg_replace('/-{2,}/i', '-',$urlKey);
        }

        $this->setUrlKey(strtolower($urlKey));
        $this->processImages();

        return parent::beforeSave();
    }


    protected function processImages()
    {
        $mediaGalleryData = $this->getData('stockist_images');

        if (!is_array($mediaGalleryData)) {
            return $this;
        }

        $newImages = [];
        foreach ($mediaGalleryData as $image) {
            if (!$this->isTmpFileAvailable($image)) {
                $newImages[] = $image;
                continue;
            }

            $newImgRelativePath = $this->imageUploader->moveFileFromTmp($image['name'], true);
            $image['url'] = '/media/' . $newImgRelativePath;
            $newImages[] = [
                'url' => $image['url'],
                'name' => $image['name'],
                'size' => $image['size'],
                'type' => $image['type'],
            ];
        }

        $this->setData('stockist_images', json_encode($newImages));

        return $this;
    }

    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value['tmp_name']);
    }
}
