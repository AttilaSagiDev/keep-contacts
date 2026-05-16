<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Ui\Component\Listing\Column\Store;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\System\Store as SystemStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Ui\Component\Listing\Column\Store\Options;

class OptionsTest extends TestCase
{
    /**
     * @var Options
     */
    private Options $optionsProvider;

    /**
     * @var MockObject|SystemStore
     */
    private MockObject|SystemStore $systemStoreMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->systemStoreMock = $this->createMock(SystemStore::class);

        $this->optionsProvider = $objectManager->getObject(
            Options::class,
            [
                'systemStore' => $this->systemStoreMock
            ]
        );
    }

    public function testToOptionArrayPrependsAllStoreViews()
    {
        $this->systemStoreMock->method('getWebsiteCollection')->willReturn([]);
        $this->systemStoreMock->method('getGroupCollection')->willReturn([]);
        $this->systemStoreMock->method('getStoreCollection')->willReturn([]);

        $result = $this->optionsProvider->toOptionArray();

        $this->assertIsArray($result);

        $allStoreViewsOption = $result[0];
        $this->assertEquals('0', $allStoreViewsOption['value']);
        $this->assertEquals('All Store Views', (string)$allStoreViewsOption['label']);
    }

    public function testToOptionArrayCachesResult()
    {
        $this->systemStoreMock->expects($this->once())
            ->method('getWebsiteCollection')
            ->willReturn([]);

        $this->optionsProvider->toOptionArray();
        $this->optionsProvider->toOptionArray();
    }
}
