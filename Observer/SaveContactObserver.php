<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Observer;

use Magento\Framework\Event\ObserverInterface;
use Space\KeepContacts\Model\ContactFactory;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Exception;

class SaveContactObserver implements ObserverInterface
{
    /**
     * @var ContactFactory
     */
    private ContactFactory $contactFactory;

    /**
     * @var ContactRepositoryInterface
     */
    private ContactRepositoryInterface $contactRepository;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ContactFactory $contactFactory
     * @param ContactRepositoryInterface $contactRepository
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContactFactory $contactFactory,
        ContactRepositoryInterface $contactRepository,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->contactFactory = $contactFactory;
        $this->contactRepository = $contactRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Save contact observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isEnabled()) {
            try {
                $params = $this->validatedParams($observer->getEvent()->getData('request')->getParams());
                if (!empty($params)) {
                    $contact = $this->contactFactory->create();
                    $contact->setName($params['name']);
                    $contact->setEmail($params['email']);
                    $contact->setTelephone($params['telephone']);
                    $contact->setComment($params['comment']);
                    $contact->setIsAnswered(false);

                    $this->contactRepository->save($contact);
                }
            } catch (LocalizedException $e) {
                $this->logger->error($e->getMessage());
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * Validate params
     *
     * @param array $params
     * @return array
     * @throws LocalizedException
     */
    private function validatedParams(array $params): array
    {
        if (trim($params['name']) === ''
            || trim($params['comment']) === ''
            || !str_contains($params['email'], '@')
            || trim($params['hideit']) !== ''
        ) {
            throw new LocalizedException(__('Unable to save contact information.'));
        }

        return $params;
    }
}
