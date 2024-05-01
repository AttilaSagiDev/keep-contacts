<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model;

use Space\KeepContacts\Api\Data\ContactSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with contact search results
 */
class ContactSearchResults extends SearchResults implements ContactSearchResultsInterface
{
}
