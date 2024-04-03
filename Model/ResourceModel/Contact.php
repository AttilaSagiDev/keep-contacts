<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param EntityManager $entityManager
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        string $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('space_keep_contacts', ContactInterface::CONTACT_ID);
    }

    /**
     * Save an object
     *
     * @param AbstractModel $object
     * @return Contact
     * @throws \Exception
     */
    public function save(AbstractModel $object): Contact
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object): AbstractDb|Contact|static
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
