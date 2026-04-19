<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Ui\Component\Form\Contact;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Model\Contact;
use Space\KeepContacts\Model\ResourceModel\Contact\Collection;
use Space\KeepContacts\Model\ResourceModel\Contact\CollectionFactory;
use Space\KeepContacts\Ui\Component\Form\Contact\DataProvider;

class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private DataProvider $dataProvider;

    /**
     * @var MockObject|CollectionFactory
     */
    private MockObject|CollectionFactory $collectionFactoryMock;

    /**
     * @var MockObject|Collection
     */
    private MockObject|Collection $collectionMock;

    /**
     * @var MockObject|DataPersistorInterface
     */
    private MockObject|DataPersistorInterface $dataPersistorMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->collectionMock = $this->createMock(Collection::class);
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionFactoryMock->method('create')->willReturn($this->collectionMock);

        $this->dataPersistorMock = $this->getMockForAbstractClass(DataPersistorInterface::class);

        $this->dataProvider = $objectManager->getObject(
            DataProvider::class,
            [
                'name' => 'contact_form_data_provider',
                'primaryFieldName' => 'contact_id',
                'requestFieldName' => 'contact_id',
                'contactCollectionFactory' => $this->collectionFactoryMock,
                'dataPersistor' => $this->dataPersistorMock
            ]
        );
    }

    public function testGetData()
    {
        $contactId = 1;
        $contactData = ['contact_id' => 1, 'name' => 'John Doe'];

        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getData'])
            ->getMock();

        $contactMock->method('getId')->willReturn($contactId);
        $contactMock->method('getData')->willReturn($contactData);

        $this->collectionMock->method('getItems')->willReturn([$contactMock]);
        $this->dataPersistorMock->method('get')->willReturn(null);

        $result = $this->dataProvider->getData();
        $this->assertArrayHasKey($contactId, $result);
    }

    public function testGetDataWithPersistentData()
    {
        $this->collectionMock->method('getItems')->willReturn([]);

        $persistentData = ['contact_id' => 2, 'name' => 'Jane Doe'];
        $this->dataPersistorMock->method('get')->with('contact')->willReturn($persistentData);

        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'getData', 'setData'])
            ->getMock();

        $contactMock->method('getId')->willReturn(2);
        $contactMock->method('getData')->willReturn($persistentData);
        $contactMock->method('setData')->willReturnSelf();

        $this->collectionMock->method('getNewEmptyItem')->willReturn($contactMock);

        $result = $this->dataProvider->getData();
        $this->assertArrayHasKey(2, $result);
    }

    public function testGetDataCachesResult()
    {
        $this->collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $this->dataPersistorMock->expects($this->once())
            ->method('get')
            ->with('contact')
            ->willReturn(null);

        $this->dataProvider->getData();

        $this->dataProvider->getData();
    }
}
