<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsAnswered implements OptionSourceInterface
{
    /**
     * Answers values
     */
    public const IS_ANSWERED_NO = 0;
    public const IS_ANSWERED_YES = 1;

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $availableAnswers = $this->getAvailableAnswers();
        $options = [];
        foreach ($availableAnswers as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * Get available answers
     *
     * @return array
     */
    public function getAvailableAnswers(): array
    {
        return [self::IS_ANSWERED_NO => __('No'), self::IS_ANSWERED_YES => __('Yes')];
    }
}
