<?php

namespace Fyb\StockistLocator\Ui\DataProvider\Form;

class StockistPageDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Fyb\StockistLocator\Model\ResourceModel\Localstockists\Collection
     */
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Fyb\StockistLocator\Model\ResourceModel\StockistPage\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
            $this->loadedData[$item->getId()]['stockist_images'] = json_decode((string)$item->getData('stockist_images'), true);
        }

        return $this->loadedData;
    }
}
