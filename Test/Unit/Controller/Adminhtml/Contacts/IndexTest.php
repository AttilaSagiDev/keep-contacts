<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Controller\Adminhtml\Contacts;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Controller\Adminhtml\Contacts\Index;

class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private Index $controller;

    /**
     * @var MockObject
     */
    private MockObject $resultPageFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $resultPageMock;

    /**
     * @var MockObject|DataPersistorInterface
     */
    private MockObject|DataPersistorInterface $dataPersistorMock;

    /**
     * @var MockObject|ObjectManagerInterface
     */
    private MockObject|ObjectManagerInterface $objectManagerMock;

    protected function setUp(): void
    {
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataPersistorMock = $this->getMockForAbstractClass(DataPersistorInterface::class);
        $this->objectManagerMock = $this->getMockForAbstractClass(ObjectManagerInterface::class);

        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $responseMock = $this->getMockForAbstractClass(ResponseInterface::class);
        $messageManagerMock = $this->getMockForAbstractClass(MessageManagerInterface::class);

        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getObjectManager')->willReturn($this->objectManagerMock);
        $contextMock->method('getRequest')->willReturn($requestMock);
        $contextMock->method('getResponse')->willReturn($responseMock);
        $contextMock->method('getMessageManager')->willReturn($messageManagerMock);

        $this->controller = new Index(
            $contextMock,
            $this->resultPageFactoryMock
        );
    }

    public function testExecute()
    {
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $this->resultPageMock->method('setActiveMenu')->willReturnSelf();

        $this->resultPageMock->method('addBreadcrumb')->willReturnSelf();

        $pageConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $titleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultPageMock->method('getConfig')->willReturn($pageConfigMock);
        $pageConfigMock->method('getTitle')->willReturn($titleMock);

        $titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Contacts'));

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with(DataPersistorInterface::class)
            ->willReturn($this->dataPersistorMock);

        $this->dataPersistorMock->expects($this->once())
            ->method('clear')
            ->with('contact');

        $result = $this->controller->execute();
        $this->assertSame($this->resultPageMock, $result);
    }
}
