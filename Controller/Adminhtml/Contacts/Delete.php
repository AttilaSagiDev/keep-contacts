<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Controller\Adminhtml\Contacts;

use Space\KeepContacts\Controller\Adminhtml\Contacts;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Contacts implements HttpPostActionInterface
{
    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ContactRepositoryInterface $contactRepository
     */
    public function __construct(
        Context $context,
        ContactRepositoryInterface $contactRepository
    ) {
        $this->contactRepository = $contactRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam(ContactInterface::CONTACT_ID);
        if ($id) {
            try {
                $contact = $this->contactRepository->getById($id);
                $this->contactRepository->delete($contact);

                $this->messageManager->addSuccessMessage(__('You deleted the contact.'));

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while trying to delete contact.')
                );
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the contact to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
