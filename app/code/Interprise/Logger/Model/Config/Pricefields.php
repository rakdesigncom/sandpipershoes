<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pricefields
 *
 * @author mm
 */
namespace Interprise\Logger\Model\Config;

class Pricefields implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [['value' => 'Retail', 'label' => __('Retail')],['value' => 'Wholesale', 'label' => __('Wholesale')]];
    }

    public function toArray()
    {
        return ['Retail' => __('Retail'),'Wholesale' => __('Wholesale')];
    }
}
