<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Controller\Adminhtml\Contacts;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Controller\Adminhtml\Contacts\MassSetIsAnswered;
use Space\KeepContacts\Model\ResourceModel\Contact\Collection;
use Space\KeepContacts\Model\ResourceModel\Contact\CollectionFactory;
use Space\KeepContacts\Model\Contact;

class MassSetIsAnsweredTest extends TestCase
{
    /**
     * @var MassSetIsAnswered
     */
    private MassSetIsAnswered $controller;

    /**
     * @var MockObject|Filter
     */
    private MockObject|Filter $filterMock;

    /**
     * @var MockObject|CollectionFactory
     */
    private MockObject|CollectionFactory $collectionFactoryMock;

    /**
     * @var MockObject|Collection
     */
    private MockObject|Collection $collectionMock;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    private MockObject|ContactRepositoryInterface $contactRepositoryMock;

    /**
     * @var MockObject|ResultFactory
     */
    private MockObject|ResultFactory $resultFactoryMock;

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

        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactRepositoryMock = $this->getMockBuilder(ContactRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMockForAbstractClass();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);

        $this->controller = $objectManager->getObject(
            MassSetIsAnswered::class,
            [
                'context' => $contextMock,
                'filter' => $this->filterMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'contactRepository' => $this->contactRepositoryMock
            ]
        );
    }

    public function testExecuteSuccess()
    {
        $collectionSize = 2;

        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setIsAnswered'])
            ->getMock();

        $contactMock->expects($this->exactly($collectionSize))
            ->method('setIsAnswered')
            ->with(true)
            ->willReturnSelf();

        $this->collectionMock->method('getSize')->willReturn($collectionSize);
        $this->collectionMock->method('getIterator')->willReturn(new \ArrayIterator([$contactMock, $contactMock]));

        $this->collectionFactoryMock->method('create')->willReturn($this->collectionMock);
        $this->filterMock->method('getCollection')->willReturn($this->collectionMock);

        $this->contactRepositoryMock->expects($this->exactly($collectionSize))
            ->method('save')
            ->with($contactMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 record(s) have been updated.', $collectionSize));

        $this->resultFactoryMock->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirectMock);

        $this->resultRedirectMock->method('setPath')->with('*/*/')->willReturnSelf();

        $this->assertEquals($this->resultRedirectMock, $this->controller->execute());
    }

    public function testExecuteWithException()
    {
        $this->collectionFactoryMock->method('create')->willReturn($this->collectionMock);
        $this->filterMock->method('getCollection')->willThrowException(new \Exception('Error'));

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage');

        $this->resultFactoryMock->method('create')->willReturn($this->resultRedirectMock);
        $this->resultRedirectMock->method('setPath')->willReturnSelf();

        $this->controller->execute();
    }
}
