<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model;

use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Model\ResourceModel\Contact as ResourceModelContact;
use Space\KeepContacts\Model\ContactFactory;
use Space\KeepContacts\Model\ResourceModel\Contact\CollectionFactory as ContactCollectionFactory;
use Space\KeepContacts\Api\Data\ContactSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Space\KeepContacts\Api\Data\ContactInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\App\ObjectManager;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Space\KeepContacts\Api\Data\ContactSearchResultsInterface;
use Space\KeepContacts\Model\ResourceModel\Contact\Collection;
use Magento\Framework\Exception\CouldNotDeleteException;

class ContactRepository implements ContactRepositoryInterface
{
    /**
     * @var ResourceModelContact
     */
    private ResourceModelContact $resource;

    /**
     * @var ContactFactory
     */
    private ContactFactory $contactFactory;

    /**
     * @var ContactCollectionFactory
     */
    private ContactCollectionFactory $contactCollectionFactory;

    /**
     * @var ContactSearchResultsInterfaceFactory
     */
    private ContactSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private DataObjectProcessor $dataObjectProcessor;

    /**
     * @var ContactInterfaceFactory
     */
    private ContactInterfaceFactory $dataContactFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @param ResourceModelContact $resource
     * @param ContactFactory $contactFactory
     * @param ContactInterfaceFactory $dataContactFactory
     * @param ContactCollectionFactory $contactCollectionFactory
     * @param ContactSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface|null $hydrator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceModelContact $resource,
        ContactFactory $contactFactory,
        ContactInterfaceFactory $dataContactFactory,
        ContactCollectionFactory $contactCollectionFactory,
        ContactSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->contactFactory = $contactFactory;
        $this->contactCollectionFactory = $contactCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataContactFactory = $dataContactFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * Save contact data
     *
     * @param ContactInterface $contact
     * @return Contact
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function save(ContactInterface $contact): Contact
    {
        if (empty($contact->getStoreId())) {
            $contact->setStoreId((int)$this->storeManager->getStore()->getId());
        }

        if ($contact->getId() && $contact instanceof Contact && !$contact->getOrigData()) {
            $contact = $this->hydrator->hydrate($this->getById($contact->getId()), $this->hydrator->extract($contact));
        }

        try {
            $this->resource->save($contact);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $contact;
    }

    /**
     * Load contact data by given contact ID
     *
     * @param int $contactId
     * @return Contact
     * @throws NoSuchEntityException
     */
    public function getById(int $contactId): ContactInterface
    {
        $contact = $this->contactFactory->create();
        $this->resource->load($contact, $contactId);
        if (!$contact->getId()) {
            throw new NoSuchEntityException(__('The contact with the "%1" ID doesn\'t exist.', $contactId));
        }

        return $contact;
    }

    /**
     * Load contact data collection by given search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ContactSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ContactSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->contactCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ContactSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete contact
     *
     * @param ContactInterface $contact
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ContactInterface $contact): bool
    {
        try {
            $this->resource->delete($contact);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete contact by given contact ID
     *
     * @param int $contactId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $contactId): bool
    {
        return $this->delete($this->getById($contactId));
    }
}
