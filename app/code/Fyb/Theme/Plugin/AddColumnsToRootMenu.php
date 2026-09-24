<?php

namespace Fyb\Theme\Plugin;

use Rootways\Megamenu\Model\Attribute\Numberofcol;

class AddColumnsToRootMenu
{
    /**
     * @param Numberofcol $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetAllOptions(Numberofcol $subject, array $result): array
    {
        $result[] = ['value' => '7', 'label' => '7'];

        return $result;
    }
}
