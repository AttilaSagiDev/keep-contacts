<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Controller\Adminhtml\Contacts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Controller\Adminhtml\Contacts\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private Delete $controller;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $contactRepositoryMock;

    /**
     * @var MockObject|RequestInterface
     */
    private MockObject|RequestInterface $requestMock;

    /**
     * @var MockObject|RedirectFactory
     */
    private MockObject|RedirectFactory $resultRedirectFactoryMock;

    /**
     * @var MockObject|Redirect
     */
    private MockObject|Redirect $resultRedirectMock;

    /**
     * @var MockObject|ManagerInterface
     */
    private MockObject|ManagerInterface $messageManagerMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contactRepositoryMock = $this->getMockBuilder(ContactRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactoryMock->method('create')->willReturn($this->resultRedirectMock);

        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);

        $this->controller = $objectManager->getObject(
            Delete::class,
            [
                'context' => $contextMock,
                'contactRepository' => $this->contactRepositoryMock
            ]
        );
    }

    public function testExecuteSuccess()
    {
        $id = 123;
        $this->requestMock->method('getParam')
            ->with(ContactInterface::CONTACT_ID)
            ->willReturn($id);

        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $this->contactRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($id)
            ->willReturn($contactMock);

        $this->contactRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($contactMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You deleted the contact.'));

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirectMock, $this->controller->execute());
    }

    public function testExecuteWithMissingId()
    {
        $this->requestMock->method('getParam')->willReturn(null);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('We can\'t find the contact to delete.'));

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirectMock, $this->controller->execute());
    }

    public function testExecuteWithException()
    {
        $id = 5;
        $this->requestMock->method('getParam')->willReturn($id);

        $this->contactRepositoryMock->method('getById')
            ->willThrowException(new \Exception('Error message'));

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage');

        $this->resultRedirectMock->method('setPath')->willReturnSelf();

        $this->controller->execute();
    }
}
