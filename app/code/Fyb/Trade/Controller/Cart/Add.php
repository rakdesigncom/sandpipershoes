<?php

namespace Fyb\Trade\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\LocalizedToNormalized;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    private $quantityProcessor;

    protected $productFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface
        $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ?RequestQuantityProcessor $quantityProcessor = null
    ) {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $productRepository, $quantityProcessor);

        $this->productFactory = $productFactory;
        $this->quantityProcessor = $quantityProcessor
            ?? ObjectManager::getInstance()->get(RequestQuantityProcessor::class);
    }

    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId, true);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $multipleAttributeId = $params['multiple-attribute-id'] ?? '';

        if (!$multipleAttributeId) {
            return parent::execute();
        }

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(
                __('Your session has expired')
            );

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $attributesQty = $params['super_attribute'][$multipleAttributeId] ?? '';
            if (!$attributesQty) {
                return $this->goBack();
            }

            $attributesQty = json_decode($attributesQty, true);
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            foreach ($attributesQty as $attributeId => $qty) {
                $product = $this->productFactory->create()->setStoreId($storeId)->load($params['product']);

                if (!$product || !$product->getId()) {
                    return $this->goBack();
                }

                $filter = new LocalizedToNormalized(
                    [
                        'locale' => $this->_objectManager->get(
                            \Magento\Framework\Locale\ResolverInterface::class
                        )->getLocale(),
                    ]
                );
                $qty = $this->quantityProcessor->prepareQuantity($qty);

                $params['super_attribute'][$multipleAttributeId] = $attributeId;
                $params['qty'] = $filter->filter($qty);

                $superAttributes = [];
                foreach ($params['super_attribute'] as $keyS => $valS) {
                    $superAttributes[(string)$keyS] = (string)$valS;
                }

                $params = [
                    'uenc' => $params['uenc'],
                    'product' => $params['product'],
                    'selected_configurable_option' => $params['selected_configurable_option'],
                    'item' => $params['item'],
                    'form_key' => $params['form_key'],
                    'super_attribute' => $superAttributes,
                    'qty' => $params['qty'],
                ];

                $this->cart->addProduct($product, $params);
            }

            $this->cart->save();

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if ($this->shouldRedirectToCart()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                } else {
                    $this->messageManager->addComplexSuccessMessage(
                        'addCartSuccessMessage',
                        [
                            'product_name' => $product->getName(),
                            'cart_url' => $this->getCartUrl(),
                        ]
                    );
                }
                if ($this->cart->getQuote()->getHasError()) {
                    $errors = $this->cart->getQuote()->getErrors();
                    foreach ($errors as $error) {
                        $this->messageManager->addErrorMessage($error->getText());
                    }
                }

                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            print_r($e->getMessage()); exit;
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $url = $this->_redirect->getRedirectUrl($this->getCartUrl());
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            print_r($e->getMessage()); exit;
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item to your shopping cart right now.')
            );
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);

            return $this->goBack();
        }

        return $this->getResponse();
    }

    /**
     * Is redirect should be performed after the product was added to cart.
     *
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }
}
