<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Ui\Component\Listing\Column\Store;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptionsColumn;

class Options extends StoreOptionsColumn
{
    /**
     * All Store Views value
     */
    private const ALL_STORE_VIEWS = '0';

    /**
     * All Store Views label
     */
    private const ALL_STORE_VIEWS_LABEL = 'All Store Views';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions[self::ALL_STORE_VIEWS_LABEL]['label'] = __('All Store Views');
        $this->currentOptions[self::ALL_STORE_VIEWS_LABEL]['value'] = self::ALL_STORE_VIEWS;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
