<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for contact search results
 * @api
 * @note Had to remove short return type declaration because of web api support
 */
interface ContactSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get contacts list
     *
     * @return \Space\KeepContacts\Api\Data\ContactInterface[]
     */
    public function getItems();

    /**
     * Set contacts list
     *
     * @param \Space\KeepContacts\Api\Data\ContactInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
