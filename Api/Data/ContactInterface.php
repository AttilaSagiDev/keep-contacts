<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Api\Data;

interface ContactInterface
{
    /**
     * Constants for keys of data array
     */
    public const CONTACT_ID = 'contact_id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const TELEPHONE = 'telephone';
    public const COMMENT = 'comment';
    public const ANSWER = 'answer';
    public const CREATION_TIME = 'creation_time';
    public const UPDATE_TIME = 'update_time';
    public const IS_ANSWERED = 'is_answered';
    public const STORE_ID = 'store_id';

    /**
     * Get contact ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get contact name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Get contact email
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Get contact telephone
     *
     * @return string|null
     */
    public function getTelephone(): ?string;

    /**
     * Get contact comment
     *
     * @return string|null
     */
    public function getComment(): ?string;

    /**
     * Get contact answer
     *
     * @return string|null
     */
    public function getAnswer(): ?string;

    /**
     * Get contact creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string;

    /**
     * Get contact update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string;

    /**
     * Is contact answered
     *
     * @return bool|null
     */
    public function isAnswered(): ?bool;

    /**
     * Get contact store ID
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * Set contact ID
     *
     * @param int $value
     * @return ContactInterface
     */
    public function setId(int $value): ContactInterface;

    /**
     * Set contact name
     *
     * @param string $name
     * @return ContactInterface
     */
    public function setName(string $name): ContactInterface;

    /**
     * Set contact email
     *
     * @param string $email
     * @return ContactInterface
     */
    public function setEmail(string $email): ContactInterface;

    /**
     * Set contact telephone
     *
     * @param string $telephone
     * @return ContactInterface
     */
    public function setTelephone(string $telephone): ContactInterface;

    /**
     * Set contact comment
     *
     * @param string $comment
     * @return ContactInterface
     */
    public function setComment(string $comment): ContactInterface;

    /**
     * Set contact answer
     *
     * @param string $answer
     * @return ContactInterface
     */
    public function setAnswer(string $answer): ContactInterface;

    /**
     * Set contact creation time
     *
     * @param string $creationTime
     * @return ContactInterface
     */
    public function setCreationTime(string $creationTime): ContactInterface;

    /**
     * Set contact update time
     *
     * @param string $updateTime
     * @return ContactInterface
     */
    public function setUpdateTime(string $updateTime): ContactInterface;

    /**
     * Set is contact answered
     *
     * @param bool|int $isAnswered
     * @return ContactInterface
     */
    public function setIsAnswered(bool $isAnswered): ContactInterface;

    /**
     * Set contact store ID
     *
     * @param int $storeId
     * @return ContactInterface
     */
    public function setStoreId(int $storeId): ContactInterface;
}
