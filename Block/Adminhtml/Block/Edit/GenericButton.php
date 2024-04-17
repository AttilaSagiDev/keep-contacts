<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Block\Adminhtml\Block\Edit;

use Magento\Backend\Block\Widget\Context;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var ContactRepositoryInterface
     */
    protected ContactRepositoryInterface $contactRepository;

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
        $this->context = $context;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Get contact ID
     *
     * @return int|null
     */
    public function getContactId(): ?int
    {
        try {
            return $this->contactRepository->getById(
                (int)$this->context->getRequest()->getParam('contact_id')
            )->getId();
        } catch (NoSuchEntityException|LocalizedException $e) {
            // nothing needed here
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
