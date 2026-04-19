<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Block\Adminhtml\Block\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Block\Adminhtml\Block\Edit\BackButton;

class BackButtonTest extends TestCase
{
    /**
     * @var BackButton
     */
    private BackButton $button;

    /**
     * @var MockObject|Context
     */
    private MockObject|Context $contextMock;

    /**
     * @var MockObject|UrlInterface
     */
    private MockObject|UrlInterface $urlBuilderMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);

        $contactRepositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->button = $objectManager->getObject(
            BackButton::class,
            [
                'context' => $this->contextMock,
                'contactRepository' => $contactRepositoryMock,
                'logger' => $loggerMock
            ]
        );
    }

    public function testGetButtonData()
    {
        $backUrl = 'http://example.com/admin/contacts/index/';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/', [])
            ->willReturn($backUrl);

        $result = $this->button->getButtonData();

        $this->assertIsArray($result);
        $this->assertEquals('back', $result['class']);
        $this->assertEquals(10, $result['sort_order']);
        $this->assertStringContainsString($backUrl, $result['on_click']);

        $this->assertEquals('Back', (string)$result['label']);
    }
}
