<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Controller\Adminhtml\Contacts;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Controller\Adminhtml\Contacts\Edit;

class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    private Edit $controller;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $repositoryMock;

    /**
     * @var MockObject
     */
    private MockObject $registryMock;

    /**
     * @var MockObject
     */
    private MockObject $pageFactoryMock;

    /**
     * @var MockObject|RequestInterface
     */
    private MockObject|RequestInterface $requestMock;

    /**
     * @var MockObject|ManagerInterface
     */
    private MockObject|ManagerInterface $messageManagerMock;

    /**
     * @var MockObject
     */
    private MockObject $redirectFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $resultPageMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $this->registryMock = $this->getMockBuilder(Registry::class)->disableOriginalConstructor()->getMock();
        $this->pageFactoryMock = $this->getMockBuilder(PageFactory::class)->disableOriginalConstructor()->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->redirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)->disableOriginalConstructor()->getMock();
        $this->resultPageMock = $this->getMockBuilder(Page::class)->disableOriginalConstructor()->getMock();

        $this->resultPageMock->method('setActiveMenu')->willReturnSelf();
        $this->resultPageMock->method('addBreadcrumb')->willReturnSelf();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($this->redirectFactoryMock);

        $this->controller = new Edit(
            $contextMock,
            $this->repositoryMock,
            $this->registryMock,
            $this->pageFactoryMock
        );
    }

    public function testExecuteSuccess()
    {
        $contactId = 123;
        $contactName = 'John Doe';

        $this->requestMock->method('getParam')->with(ContactInterface::CONTACT_ID)->willReturn($contactId);

        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $contactMock->method('getId')->willReturn($contactId);
        $contactMock->method('getName')->willReturn($contactName);

        $this->repositoryMock->method('getById')->with($contactId)->willReturn($contactMock);

        $this->registryMock->expects($this->once())
            ->method('register')
            ->with('contact', $contactMock);

        $this->pageFactoryMock->method('create')->willReturn($this->resultPageMock);

        $configMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $titleMock = $this->getMockBuilder(Title::class)->disableOriginalConstructor()->getMock();
        $this->resultPageMock->method('getConfig')->willReturn($configMock);
        $configMock->method('getTitle')->willReturn($titleMock);

        $titleMock->expects($this->exactly(2))->method('prepend');

        $result = $this->controller->execute();
        $this->assertSame($this->resultPageMock, $result);
    }

    public function testExecuteRedirectWhenNotFound()
    {
        $contactId = 1;
        $this->requestMock->method('getParam')->willReturn($contactId);

        $this->repositoryMock->method('getById')
            ->willThrowException(new NoSuchEntityException(__('Not Found')));

        $this->messageManagerMock->expects($this->once())->method('addErrorMessage');

        $redirectMock = $this->getMockBuilder(Redirect::class)->disableOriginalConstructor()->getMock();
        $this->redirectFactoryMock->method('create')->willReturn($redirectMock);
        $redirectMock->expects($this->once())->method('setPath')->with('*/*/')->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertSame($redirectMock, $result);
    }

    public function testExecuteRedirectWhenNoId()
    {
        $this->requestMock->method('getParam')->willReturn(null);

        $redirectMock = $this->getMockBuilder(Redirect::class)->disableOriginalConstructor()->getMock();
        $this->redirectFactoryMock->method('create')->willReturn($redirectMock);
        $redirectMock->method('setPath')->with('*/*/')->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertSame($redirectMock, $result);
    }
}
