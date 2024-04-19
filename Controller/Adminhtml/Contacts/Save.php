<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Controller\Adminhtml\Contacts;

use Magento\Framework\Exception\NoSuchEntityException;
use Space\KeepContacts\Controller\Adminhtml\Contacts;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Model\Source\IsAnswered;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;

class Save extends Contacts implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        ContactRepositoryInterface $contactRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->contactRepository = $contactRepository;
        parent::__construct($context);
    }

    /**
     * Save and send answer
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data[ContactInterface::IS_ANSWERED])
                && $data[ContactInterface::IS_ANSWERED] === true
            ) {
                $data[ContactInterface::IS_ANSWERED] = IsAnswered::IS_ANSWERED_YES;
            }
            $id = (int)$this->getRequest()->getParam(ContactInterface::CONTACT_ID);
            if ($id) {
                try {
                    $contact = $this->contactRepository->getById($id);
                    $contact->setIsAnswered((bool)$data[ContactInterface::IS_ANSWERED]);
                    $contact->setAnswer((string)$data[ContactInterface::ANSWER]);
                    $this->contactRepository->save($contact);

                    $this->messageManager->addSuccessMessage(__('You saved the contact.'));
                    $this->dataPersistor->clear('contact');
                    return $this->processContactReturn($contact, $data, $resultRedirect);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This contact no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $this->dataPersistor->set('contact', $data);
            return $resultRedirect->setPath('*/*/edit', [ContactInterface::CONTACT_ID => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process return
     *
     * @param ContactInterface $contact
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     */
    private function processContactReturn(
        ContactInterface $contact,
        array $data,
        ResultInterface $resultRedirect
    ): ResultInterface {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', [ContactInterface::CONTACT_ID => $contact->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
