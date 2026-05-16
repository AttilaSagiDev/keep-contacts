<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model\ResourceModel\Contact;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Model\ResourceModel\Contact as ResourceModelContact;
use Space\KeepContacts\Model\ResourceModel\Contact\Collection;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private Collection $model;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);

        $selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();
        $selectMock->method('from')->willReturnSelf();

        $adapterMock = $this->getMockForAbstractClass(AdapterInterface::class);
        $adapterMock->method('select')->willReturn($selectMock);

        $resourceMock = $this->getMockBuilder(ResourceModelContact::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resourceMock->method('getConnection')->willReturn($adapterMock);
        $resourceMock->method('getMainTable')->willReturn('space_keep_contacts');
        $resourceMock->method('getTable')->willReturn('space_keep_contacts');

        $entityFactoryMock = $this->getMockForAbstractClass(EntityFactoryInterface::class);
        $loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $fetchStrategyMock = $this->getMockForAbstractClass(FetchStrategyInterface::class);
        $eventManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->model = $objectManagerHelper->getObject(
            Collection::class,
            [
                'entityFactory' => $entityFactoryMock,
                'logger' => $loggerMock,
                'fetchStrategy' => $fetchStrategyMock,
                'eventManager' => $eventManagerMock,
                'resource' => $resourceMock
            ]
        );
    }

    public function testGetIdFieldName()
    {
        $this->assertEquals('contact_id', $this->model->getIdFieldName());
    }

    public function testEventProperties()
    {
        $reflection = new \ReflectionClass(Collection::class);

        $eventPrefix = $reflection->getProperty('_eventPrefix');
        $eventPrefix->setAccessible(true);

        $eventObject = $reflection->getProperty('_eventObject');
        $eventObject->setAccessible(true);

        $this->assertEquals('keep_contacts_collection', $eventPrefix->getValue($this->model));
        $this->assertEquals('contact_collection', $eventObject->getValue($this->model));
    }

    public function testGetResource()
    {
        $this->assertInstanceOf(ResourceModelContact::class, $this->model->getResource());
    }
}
