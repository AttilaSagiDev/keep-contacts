<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ContactSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get contacts list
     *
     * @return ContactInterface[]
     */
    public function getItems(): array;

    /**
     * Set contacts list
     *
     * @param ContactInterface[] $items
     * @return ContactSearchResultsInterface
     */
    public function setItems(array $items): ContactSearchResultsInterface;
}
