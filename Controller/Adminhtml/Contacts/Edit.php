<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Controller\Adminhtml\Contacts;

use Space\KeepContacts\Controller\Adminhtml\Contacts;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Space\KeepContacts\Api\Data\ContactInterface;

class Edit extends Contacts implements HttpGetActionInterface
{
    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * @var Registry
     */
    private Registry $coreRegistry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ContactRepositoryInterface $contactRepository
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ContactRepositoryInterface $contactRepository,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->contactRepository = $contactRepository;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return Page|Redirect|ResultInterface
     */
    public function execute(): Page|Redirect|ResultInterface
    {
        $id = (int)$this->getRequest()->getParam(ContactInterface::CONTACT_ID);
        if ($id) {
            try {
                $contact = $this->contactRepository->getById($id);
                if (!$contact->getId()) {
                    $this->messageManager->addErrorMessage(__('Contact with id "%1" does not exist.', $id));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }

                $this->coreRegistry->register('contact', $contact);

                $resultPage = $this->resultPageFactory->create();
                $this->initPage($resultPage)->addBreadcrumb(
                    __('Edit Contact'),
                    __('Edit Contact')
                );
                $resultPage->getConfig()->getTitle()->prepend(__('Contacts'));
                $resultPage->getConfig()->getTitle()->prepend(__('Contact from %1', $contact->getName()));

                return $resultPage;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while trying to with the contact.')
                );
            }

        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
