<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model;

use Magento\Framework\Model\AbstractModel;
use Space\KeepContacts\Api\Data\ContactInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Space\KeepContacts\Model\ResourceModel\Contact as ResourceModelContact;

class Contact extends AbstractModel implements ContactInterface, IdentityInterface
{
    /**
     * Contact cache tag
     */
    public const CACHE_TAG = 'keep_contacts';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'keep_contacts';

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModelContact::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get contact ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getData(self::CONTACT_ID);
    }

    /**
     * Get contact name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get contact email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Get contact telephone
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * Get contact comment
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Get contact creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get contact update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Is contact answered
     *
     * @return bool|null
     */
    public function isAnswered(): ?bool
    {
        return (bool)$this->getData(self::IS_ANSWERED);
    }

    /**
     * Get contact store ID
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    /**
     * Set contact ID
     *
     * @param mixed $value
     * @return ContactInterface
     */
    public function setId(mixed $value): ContactInterface
    {
        return $this->setData(self::CONTACT_ID, $value);
    }

    /**
     * Set contact name
     *
     * @param string $name
     * @return ContactInterface
     */
    public function setName(string $name): ContactInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set contact email
     *
     * @param string $email
     * @return ContactInterface
     */
    public function setEmail(string $email): ContactInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Set contact telephone
     *
     * @param string $telephone
     * @return ContactInterface
     */
    public function setTelephone(string $telephone): ContactInterface
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Set contact comment
     *
     * @param string $comment
     * @return ContactInterface
     */
    public function setComment(string $comment): ContactInterface
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Set contact creation time
     *
     * @param string $creationTime
     * @return ContactInterface
     */
    public function setCreationTime(string $creationTime): ContactInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set contact update time
     *
     * @param string $updateTime
     * @return ContactInterface
     */
    public function setUpdateTime(string $updateTime): ContactInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is contact answered
     *
     * @param bool|int $isAnswered
     * @return ContactInterface
     */
    public function setIsAnswered(bool $isAnswered): ContactInterface
    {
        return $this->setData(self::IS_ANSWERED, $isAnswered);
    }

    /**
     * Set contact store ID
     *
     * @param int $storeId
     * @return ContactInterface
     */
    public function setStoreId(int $storeId): ContactInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
