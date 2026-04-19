<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Block\Adminhtml\Block\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Block\Adminhtml\Block\Edit\DeleteButton;

class DeleteButtonTest extends TestCase
{
    /**
     * @var DeleteButton
     */
    private DeleteButton $button;

    /**
     * @var MockObject|Context
     */
    private MockObject|Context $contextMock;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $contactRepositoryMock;

    /**
     * @var MockObject|RequestInterface
     */
    private MockObject|RequestInterface $requestMock;

    /**
     * @var MockObject|UrlInterface
     */
    private MockObject|UrlInterface $urlBuilderMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->contactRepositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);

        $this->button = $objectManager->getObject(
            DeleteButton::class,
            [
                'context' => $this->contextMock,
                'contactRepository' => $this->contactRepositoryMock,
                'logger' => $loggerMock
            ]
        );
    }

    public function testGetButtonDataWithContactId()
    {
        $contactId = 5;
        $deleteUrl = "http://example.com/admin/delete/id/5";

        $this->requestMock->method('getParam')->with('contact_id')->willReturn($contactId);

        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $contactMock->method('getId')->willReturn($contactId);
        $this->contactRepositoryMock->method('getById')->with($contactId)->willReturn($contactMock);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/delete', ['contact_id' => $contactId])
            ->willReturn($deleteUrl);

        $result = $this->button->getButtonData();

        $this->assertIsArray($result);
        $this->assertEquals('delete', $result['class']);
        $this->assertStringContainsString($deleteUrl, $result['on_click']);
        $this->assertStringContainsString('deleteConfirm', $result['on_click']);
        $this->assertEquals('Delete Contact', (string)$result['label']);
    }

    public function testGetButtonDataWithoutContactId()
    {
        $this->requestMock->method('getParam')->with('contact_id')->willReturn(null);

        $result = $this->button->getButtonData();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetDeleteUrl()
    {
        $contactId = 10;
        $expectedUrl = 'http://example.com/delete/10';

        $this->requestMock->method('getParam')->willReturn($contactId);
        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $contactMock->method('getId')->willReturn($contactId);
        $this->contactRepositoryMock->method('getById')->willReturn($contactMock);

        $this->urlBuilderMock->method('getUrl')
            ->with('*/*/delete', ['contact_id' => $contactId])
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->button->getDeleteUrl());
    }
}
