<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model\ResourceModel\Contact\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Model\ResourceModel\Contact as ContactResourceModel;
use Space\KeepContacts\Model\ResourceModel\Contact\Grid\Collection;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private Collection $model;

    /**
     * @var MockObject|TimezoneInterface
     */
    private MockObject|TimezoneInterface $timeZoneMock;

    /**
     * @var MockObject|AdapterInterface
     */
    private MockObject|AdapterInterface $adapterMock;

    /**
     * @var MockObject
     */
    private MockObject $selectMock;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectMock->method('from')->willReturnSelf();

        $this->adapterMock = $this->getMockForAbstractClass(AdapterInterface::class);
        $this->adapterMock->method('select')->willReturn($this->selectMock);

        $resourceMock = $this->getMockBuilder(ContactResourceModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resourceMock->method('getConnection')->willReturn($this->adapterMock);
        $resourceMock->method('getMainTable')->willReturn('space_keep_contacts');
        $resourceMock->method('getTable')->willReturn('space_keep_contacts');

        $this->timeZoneMock = $this->getMockForAbstractClass(TimezoneInterface::class);
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
                'mainTable' => 'space_keep_contacts',
                'eventPrefix' => 'keep_contacts_grid_collection',
                'eventObject' => 'contact_grid_collection',
                'resourceModel' => ContactResourceModel::class,
                'resource' => $resourceMock,
                'timeZone' => $this->timeZoneMock
            ]
        );
    }

    public function testAddFieldToFilterConvertsTime()
    {
        $field = 'creation_time';
        $condition = ['gteq' => '2026-01-01 12:00:00'];
        $convertedTime = '2026-01-01 10:00:00';

        $this->timeZoneMock->expects($this->once())
            ->method('convertConfigTimeToUtc')
            ->with('2026-01-01 12:00:00')
            ->willReturn($convertedTime);

        $this->selectMock->expects($this->atLeastOnce())
            ->method('where')
            ->willReturnSelf();

        $this->model->addFieldToFilter($field, $condition);
    }

    public function testGetAndSetAggregations()
    {
        $aggregationsMock = $this->getMockForAbstractClass(AggregationInterface::class);
        $this->model->setAggregations($aggregationsMock);
        $this->assertSame($aggregationsMock, $this->model->getAggregations());
    }

    public function testGetTotalCount()
    {
        $this->adapterMock->method('fetchOne')->willReturn(5);
        $this->assertEquals(5, $this->model->getTotalCount());
    }
}
