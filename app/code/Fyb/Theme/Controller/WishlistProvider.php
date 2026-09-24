<?php

namespace Fyb\Theme\Controller;

class WishlistProvider extends \MageSuite\GuestWishlist\Controller\WishlistProvider
{
    public function getWishlist($wishlistId = null)
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }
        try {
            if (!$wishlistId) {
                $wishlistId = $this->request->getParam('wishlist_id');
            }
            $customerId = $this->customerSession->getCustomerId();
            $wishlist = $this->wishlistFactory->create();

            if (!$customerId) {
                $this->wishlist = $this->cookieBasedWishlistProvider->getWishlist();

                return $this->wishlist;
            }

            if ($customerId) {
                $wishlist->loadByCustomerId($customerId, true);
            } elseif ($wishlistId) {
                $wishlist->load($wishlistId);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The requested Wish List doesn\'t exist.')
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t create the Wish List right now.'));
            return false;
        }
        $this->wishlist = $wishlist;

        return $wishlist;
    }
}
