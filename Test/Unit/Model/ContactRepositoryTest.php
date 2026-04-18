<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\Data\ContactInterfaceFactory;
use Space\KeepContacts\Api\Data\ContactSearchResultsInterface;
use Space\KeepContacts\Api\Data\ContactSearchResultsInterfaceFactory;
use Space\KeepContacts\Model\Contact;
use Space\KeepContacts\Model\ContactFactory;
use Space\KeepContacts\Model\ContactRepository;
use Space\KeepContacts\Model\ResourceModel\Contact as ResourceModelContact;
use Space\KeepContacts\Model\ResourceModel\Contact\Collection;
use Space\KeepContacts\Model\ResourceModel\Contact\CollectionFactory as ContactCollectionFactory;

class ContactRepositoryTest extends TestCase
{
    /**
     * @var ContactRepository
     */
    private ContactRepository $repository;

    /**
     * @var MockObject
     */
    private MockObject $resourceMock;

    /**
     * @var MockObject
     */
    private MockObject $contactFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $dataContactFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $collectionFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $searchResultsFactoryMock;

    /**
     * @var MockObject|StoreManagerInterface
     */
    private MockObject|StoreManagerInterface $storeManagerMock;

    /**
     * @var MockObject|CollectionProcessorInterface
     */
    private MockObject|CollectionProcessorInterface $collectionProcessorMock;

    /**
     * @var MockObject|HydratorInterface
     */
    private MockObject|HydratorInterface $hydratorMock;

    protected function setUp(): void
    {
        $this->resourceMock = $this->getMockBuilder(ResourceModelContact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactFactoryMock = $this->getMockBuilder(ContactFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataContactFactoryMock = $this->getMockBuilder(ContactInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock = $this->getMockBuilder(ContactCollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchResultsFactoryMock = $this->getMockBuilder(ContactSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->hydratorMock = $this->getMockForAbstractClass(HydratorInterface::class);

        $this->repository = new ContactRepository(
            $this->resourceMock,
            $this->contactFactoryMock,
            $this->dataContactFactoryMock,
            $this->collectionFactoryMock,
            $this->searchResultsFactoryMock,
            $this->getMockBuilder(DataObjectHelper::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(DataObjectProcessor::class)->disableOriginalConstructor()->getMock(),
            $this->storeManagerMock,
            $this->collectionProcessorMock,
            $this->hydratorMock
        );
    }

    public function testGetByIdSuccess()
    {
        $contactId = 123;
        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactFactoryMock->expects($this->once())->method('create')->willReturn($contactMock);
        $this->resourceMock->expects($this->once())->method('load')->with($contactMock, $contactId);
        $contactMock->expects($this->atLeastOnce())->method('getId')->willReturn($contactId);

        $result = $this->repository->getById($contactId);
        $this->assertSame($contactMock, $result);
    }

    public function testGetByIdThrowsExceptionWhenNotFound()
    {
        $this->expectException(NoSuchEntityException::class);
        $contactId = 1;
        $contactMock = $this->getMockBuilder(Contact::class)->disableOriginalConstructor()->getMock();

        $this->contactFactoryMock->method('create')->willReturn($contactMock);
        $contactMock->method('getId')->willReturn(null);

        $this->repository->getById($contactId);
    }

    public function testSaveNewContactSuccessfully()
    {
        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $storeMock->method('getId')->willReturn(1);

        $contactMock->expects($this->once())->method('getStoreId')->willReturn(null);
        $contactMock->expects($this->once())->method('setStoreId')->with(1);

        $this->resourceMock->expects($this->once())->method('save')->with($contactMock);

        $result = $this->repository->save($contactMock);
        $this->assertSame($contactMock, $result);
    }

    public function testGetList()
    {
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $searchResultsMock = $this->getMockForAbstractClass(ContactSearchResultsInterface::class);

        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collectionMock);
        $this->searchResultsFactoryMock->expects($this->once())->method('create')->willReturn($searchResultsMock);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $items = [$this->getMockBuilder(Contact::class)->disableOriginalConstructor()->getMock()];
        $collectionMock->expects($this->once())->method('getItems')->willReturn($items);
        $collectionMock->expects($this->once())->method('getSize')->willReturn(1);

        $searchResultsMock->expects($this->once())->method('setSearchCriteria')->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())->method('setItems')->with($items);
        $searchResultsMock->expects($this->once())->method('setTotalCount')->with(1);

        $result = $this->repository->getList($searchCriteriaMock);
        $this->assertSame($searchResultsMock, $result);
    }

    public function testDeleteById()
    {
        $contactId = 5;
        $contactMock = $this->getMockBuilder(Contact::class)->disableOriginalConstructor()->getMock();
        $contactMock->method('getId')->willReturn($contactId);

        $this->contactFactoryMock->method('create')->willReturn($contactMock);
        $this->resourceMock->expects($this->once())->method('load')->with($contactMock, $contactId);
        $this->resourceMock->expects($this->once())->method('delete')->with($contactMock);

        $this->assertTrue($this->repository->deleteById($contactId));
    }
}
