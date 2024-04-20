<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\App\Area;

class SendMail
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param ConfigInterface $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigInterface $config,
        StoreManagerInterface $storeManager
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param ContactInterface $contact
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendKeepContactsEmail(ContactInterface $contact): void
    {
        if (!empty($contact->getAnswer())) {
            $ccEmail = $this->config->getCcEmail() ? $this->config->getCcEmail() : '';
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->config->getEmailTemplate()
            )->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getStoreId(),
                ]
            )->setTemplateVars(
                [
                    'store' => $this->storeManager->getStore(),
                    'customer_name' => $contact->getName(),
                    'customer_email' => $contact->getEmail(),
                    'customer_telephone' => $contact->getTelephone(),
                    'customer_comment' => $contact->getComment(),
                    'answer' => $contact->getAnswer(),
                    'include_contact_comment' => $this->config->isIncludeContactComment()
                ]
            )->setReplyTo(
                $this->config->getSenderEmail()
            )->setFromByScope(
                $this->config->getSenderEmail()
            )->addTo(
                $contact->getEmail()
            )->addCc(
                $ccEmail
            )->getTransport();

            $transport->sendMessage();
        } else {
            throw new LocalizedException(__('Answer is empty'));
        }
    }
}
