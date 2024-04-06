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
     * Check if keep contacts module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;
}
