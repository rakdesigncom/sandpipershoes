<?php

namespace Fyb\Theme\Plugin\Blog;

use Mageplaza\Blog\Model\Config\Source\SideBarLR;

class SidebarOptionsAfter
{
    /**
     * @param SideBarLR $subject
     * @param array $result
     *
     * @return array
     */
    public function afterToArray(SideBarLR $subject, array $result): array
    {
        $result['1column'] =  __('No Sidebar');
        return $result;
    }
}
