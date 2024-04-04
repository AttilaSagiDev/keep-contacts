<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model\ResourceModel\Contact;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Space\KeepContacts\Model\Contact;
use Space\KeepContacts\Model\ResourceModel\Contact as ResourceModelContact;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'contact_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'keep_contacts_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'contact_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Contact::class, ResourceModelContact::class);
    }
}
