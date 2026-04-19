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
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Block\Adminhtml\Block\Edit\GenericButton;

class GenericButtonTest extends TestCase
{
    /**
     * @var GenericButton
     */
    private GenericButton $button;

    /**
     * @var MockObject|Context
     */
    private MockObject|Context $contextMock;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $contactRepositoryMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private MockObject|LoggerInterface $loggerMock;

    /**
     * @var MockObject|ContactInterface
     */
    private MockObject|RequestInterface $requestMock;

    /**
     * @var MockObject|UrlInterface
     */
    private MockObject|UrlInterface $urlBuilderMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contactRepositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);

        $this->button = $objectManager->getObject(
            GenericButton::class,
            [
                'context' => $this->contextMock,
                'contactRepository' => $this->contactRepositoryMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    public function testGetContactIdSuccess()
    {
        $contactId = 42;
        $this->requestMock->method('getParam')->with('contact_id')->willReturn($contactId);

        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $contactMock->method('getId')->willReturn($contactId);

        $this->contactRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($contactId)
            ->willReturn($contactMock);

        $this->assertEquals($contactId, $this->button->getContactId());
    }

    public function testGetContactIdWithException()
    {
        $this->requestMock->method('getParam')->willReturn(99);

        $exception = new NoSuchEntityException(__('Not found'));
        $this->contactRepositoryMock->method('getById')->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($exception->getMessage());

        $this->assertNull($this->button->getContactId());
    }

    public function testGetUrl()
    {
        $route = 'contacts/index/delete';
        $params = ['id' => 1];
        $expectedUrl = 'http://example.com/admin/contacts/index/delete/id/1';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($route, $params)
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->button->getUrl($route, $params));
    }
}
