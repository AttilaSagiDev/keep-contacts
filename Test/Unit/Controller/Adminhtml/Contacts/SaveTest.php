<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Controller\Adminhtml\Contacts;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Controller\Adminhtml\Contacts\Save;
use Space\KeepContacts\Model\Service\SendMail;

class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private Save $controller;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $repositoryMock;

    /**
     * @var MockObject
     */
    private MockObject $sendMailMock;

    /**
     * @var MockObject|DataPersistorInterface
     */
    private MockObject|DataPersistorInterface $dataPersistorMock;

    /**
     * @var MockObject
     */
    private MockObject $requestMock;

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
    private MockObject $redirectMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $this->sendMailMock = $this->getMockBuilder(SendMail::class)->disableOriginalConstructor()->getMock();
        $this->dataPersistorMock = $this->getMockForAbstractClass(DataPersistorInterface::class);

        $this->requestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->redirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)->disableOriginalConstructor()->getMock();
        $this->redirectMock = $this->getMockBuilder(Redirect::class)->disableOriginalConstructor()->getMock();

        $this->redirectFactoryMock->method('create')->willReturn($this->redirectMock);
        $this->redirectMock->method('setPath')->willReturnSelf();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($this->redirectFactoryMock);

        $this->controller = new Save(
            $contextMock,
            $this->dataPersistorMock,
            $this->repositoryMock,
            $this->sendMailMock
        );
    }

    public function testExecuteSaveAndSendEmail()
    {
        $id = 1;
        $postData = [
            ContactInterface::IS_ANSWERED => '1',
            ContactInterface::ANSWER => 'Test Answer',
            'back' => 'continue'
        ];

        $this->requestMock->method('getPostValue')->willReturn($postData);
        $this->requestMock->method('getParam')->with(ContactInterface::CONTACT_ID)->willReturn($id);

        $contactMock = $this->getMockForAbstractClass(ContactInterface::class);
        $contactMock->method('getId')->willReturn($id);

        $this->repositoryMock->method('getById')->with($id)->willReturn($contactMock);

        $contactMock->expects($this->once())->method('setIsAnswered')->with(true);
        $contactMock->expects($this->once())->method('setAnswer')->with('Test Answer');

        $this->repositoryMock->expects($this->once())->method('save')->with($contactMock);
        $this->sendMailMock->expects($this->once())->method('sendKeepContactsEmail')->with($contactMock);

        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage');
        $this->dataPersistorMock->expects($this->once())->method('clear')->with('contact');

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }

    public function testExecuteWithException()
    {
        $id = 1;
        $this->requestMock->method('getPostValue')->willReturn(['some' => 'data']);
        $this->requestMock->method('getParam')->willReturn($id);

        $this->repositoryMock->method('getById')->willThrowException(new LocalizedException(__('Error')));

        $this->messageManagerMock->expects($this->once())->method('addErrorMessage');
        $this->dataPersistorMock->expects($this->once())->method('set')->with('contact', ['some' => 'data']);

        $this->controller->execute();
    }

    public function testExecuteNoData()
    {
        $this->requestMock->method('getPostValue')->willReturn([]);
        $this->redirectMock->expects($this->once())->method('setPath')->with('*/*/');

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }
}
