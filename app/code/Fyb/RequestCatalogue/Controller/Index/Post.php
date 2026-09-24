<?php

namespace Fyb\RequestCatalogue\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

class Post extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var MailInterface
     */
    protected $mail;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param \Fyb\RequestCatalogue\Model\Mail $mail
     * @param DataPersistorInterface $dataPersistor
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Fyb\RequestCatalogue\Model\Mail $mail,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context);
        $this->mail = $mail;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $this->sendEmail(
                $this->prepareParams($this->validatedParams())
            );

            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('request_catalogue_data');

            return $this->resultRedirectFactory->create()->setPath('request-catalogue/thankyou');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('request_catalogue_data', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('request_catalogue_data', $this->getRequest()->getParams());
        }

        return $this->resultRedirectFactory->create()->setPath('request-catalogue');
    }

    /**
     * Method to send email.
     *
     * @param array $post Post data from contact form
     *
     * @return void
     */
    private function sendEmail($post)
    {
        $this->mail->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }

    private function prepareParams($params)
    {
        $notRequired = [
            'email',
            'street_address_2',
            'county',
        ];

        foreach ($params as $key => $param) {
            $params[$key] = trim($param);
        }

        foreach ($notRequired as $param) {
            if (!isset($params[$param])) {
                $params[$param] = '';
            }
        }

        return $params;
    }

    /**
     * Method to validated params.
     *
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();

        $paramsValidate = [
            'name',
            'street_address',
            'telephone',
            'post_code',
            'country_id',
        ];

        foreach ($paramsValidate as $param) {
            if (trim($request->getParam($param, '')) === '') {
                throw new LocalizedException(__('Please enter a valid params.'));
            }
        }

        if ($request->getParam('email', '') && \strpos($request->getParam('email', ''), '@') === false) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
        if (trim($request->getParam('hideit', '')) !== '') {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception();
        }

        return $request->getParams();
    }
}
