<?php

namespace Interprise\Logger\Controller\Rewrite\Address;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Delete
 *
 * @author mm
 */
class Delete extends \Magento\Customer\Controller\Address\Delete
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $address = $this->_addressRepository->getById($addressId);
                if ($address->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $this->_addressRepository->deleteById($addressId);
                    $this->messageManager->addSuccess(__('You deleted the address.'));
                    // If shiptocode exists for this address then directly inactivate the adderss in Interprise

                } else {
                    $this->messageManager->addError(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t delete the address right now.'));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
