<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Api;

use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Space\KeepContacts\Api\Data\ContactSearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Contact CRUD interface
 * @api
 * @note Had to use long return types in doc blocks because of web api
 */
interface ContactRepositoryInterface
{
    /**
     * Save contact
     *
     * @param ContactInterface $contact
     * @return \Space\KeepContacts\Api\Data\ContactInterface
     * @throws LocalizedException
     */
    public function save(ContactInterface $contact): ContactInterface;

    /**
     * Retrieve contact
     *
     * @param int $contactId
     * @return \Space\KeepContacts\Api\Data\ContactInterface
     * @throws LocalizedException
     */
    public function getById(int $contactId): ContactInterface;

    /**
     * Retrieve contacts matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Space\KeepContacts\Api\Data\ContactSearchResultsInterface
     * @throws LocalizedException
 */
    public function getList(SearchCriteriaInterface $searchCriteria): ContactSearchResultsInterface;

    /**
     * Delete contact
     *
     * @param ContactInterface $contact
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ContactInterface $contact): bool;

    /**
     * Delete contact by ID
     *
     * @param int $contactId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $contactId): bool;
}
