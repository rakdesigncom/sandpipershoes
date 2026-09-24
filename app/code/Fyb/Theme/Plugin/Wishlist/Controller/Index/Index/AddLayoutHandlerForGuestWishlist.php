<?php

namespace Fyb\Theme\Plugin\Wishlist\Controller\Index\Index;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;

class AddLayoutHandlerForGuestWishlist extends \MageSuite\GuestWishlist\Plugin\Wishlist\Controller\Index\Index\AddLayoutHandlerForGuestWishlist
{
    public function afterExecute(
        \Magento\Wishlist\Controller\Index\Index $subject,
        \Magento\Framework\View\Result\Page $result
    ) {
        $result = parent::afterExecute($subject, $result);
        $wishlistId = $subject->getRequest()->getParam('wishlist_id');
        if ($wishlistId && !$this->countItemsForGuestWishlistHelper->isCustomerGuest()) {
            $resultRedirect = ObjectManager::getInstance()->get(\Magento\Framework\Controller\ResultFactory::class)
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/wishlist');
            return $resultRedirect;
        }

        return $result;
    }
}
