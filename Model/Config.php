<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model;

use Space\KeepContacts\Api\Data\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if keep contacts module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is include contact comment
     *
     * @return bool
     */
    public function isIncludeContactComment(): bool
    {
        return (bool)$this->scopeConfig->isSetFlag(
            self::XML_PATH_INCLUDE_CONTACT_COMMENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get cc email
     *
     * @return string|null
     */
    public function getCcEmail(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CC_RECIPIENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailTemplate(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
