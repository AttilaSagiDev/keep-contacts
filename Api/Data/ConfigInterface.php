<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Api\Data;

interface ConfigInterface
{
    /**
     * Enabled config path
     */
    public const XML_PATH_ENABLED = 'keep_contacts_settings/keep_contacts_config/enabled';

    /**
     * Include contact comment path
     */
    public const XML_PATH_INCLUDE_ORIGINAL = 'keep_contacts_settings/keep_contacts_email/include_original';

    /**
     * Sender email config path
     */
    public const XML_PATH_EMAIL_SENDER = 'keep_contacts_settings/keep_contacts_email/sender_email_identity';

    /**
     * Recipient cc email config path
     */
    public const XML_PATH_CC_RECIPIENT = 'keep_contacts_settings/keep_contacts_email/cc_email';

    /**
     * Email template config path
     */
    public const XML_PATH_EMAIL_TEMPLATE = 'keep_contacts_settings/keep_contacts_email/email_template';

    /**
     * Check if keep contacts module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Is include contact comment
     *
     * @return bool
     */
    public function isIncludeContactComment(): bool;

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail(): string;

    /**
     * Get cc email
     *
     * @return string|null
     */
    public function getCcEmail(): ?string;

    /**
     * Get email template
     *
     * @return string
     */
    public function getEmailTemplate(): string;
}
