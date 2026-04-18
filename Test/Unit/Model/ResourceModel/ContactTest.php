<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Model\ResourceModel\Contact;

class ContactTest extends TestCase
{
    /**
     * @var Contact
     */
    private Contact $model;

    /**
     * @var MockObject
     */
    private MockObject $entityManagerMock;

    /**
     * @var MockObject
     */
    private MockObject $contextMock;

    /**
     * @var MockObject
     */
    private MockObject $abstractModelMock;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resourceConnectionMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockForAbstractClass(AdapterInterface::class);

        $this->contextMock->method('getResources')->willReturn($resourceConnectionMock);
        $resourceConnectionMock->method('getConnection')->willReturn($connectionMock);

        $this->abstractModelMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Contact(
            $this->contextMock,
            $this->entityManagerMock
        );
    }

    public function testSave()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($this->abstractModelMock);

        $result = $this->model->save($this->abstractModelMock);
        $this->assertSame($this->model, $result);
    }

    public function testDelete()
    {
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($this->abstractModelMock);

        $result = $this->model->delete($this->abstractModelMock);
        $this->assertSame($this->model, $result);
    }
}
