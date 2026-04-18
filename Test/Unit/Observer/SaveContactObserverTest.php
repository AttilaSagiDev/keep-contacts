<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Space\KeepContacts\Model\Contact;
use Space\KeepContacts\Model\ContactFactory;
use Space\KeepContacts\Observer\SaveContactObserver;

class SaveContactObserverTest extends TestCase
{
    /**
     * @var SaveContactObserver
     */
    private SaveContactObserver $model;

    /**
     * @var MockObject
     */
    private MockObject $contactFactoryMock;

    /**
     * @var MockObject
     */
    private MockObject $contactRepositoryMock;

    /**
     * @var MockObject
     */
    private MockObject $configMock;

    /**
     * @var MockObject
     */
    private MockObject $loggerMock;

    /**
     * @var MockObject
     */
    private MockObject $observerMock;

    protected function setUp(): void
    {
        $this->contactFactoryMock = $this->getMockBuilder(ContactFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactRepositoryMock = $this->getMockForAbstractClass(ContactRepositoryInterface::class);
        $this->configMock = $this->getMockForAbstractClass(ConfigInterface::class);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new SaveContactObserver(
            $this->contactFactoryMock,
            $this->contactRepositoryMock,
            $this->configMock,
            $this->loggerMock
        );
    }

    public function testExecuteDoesNothingWhenDisabled()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $this->contactFactoryMock->expects($this->never())->method('create');

        $this->model->execute($this->observerMock);
    }

    public function testExecuteSavesContactSuccessfully()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData'])
            ->getMock();

        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $params = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'telephone' => '123456',
            'comment' => 'Hello',
            'hideit' => ''
        ];

        $this->observerMock->expects($this->any())->method('getEvent')->willReturn($eventMock);
        $eventMock->expects($this->once())->method('getData')->with('request')->willReturn($requestMock);
        $requestMock->expects($this->once())->method('getParams')->willReturn($params);

        $contactMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactFactoryMock->expects($this->once())->method('create')->willReturn($contactMock);

        $contactMock->expects($this->once())->method('setName')->with('John Doe');
        $this->contactRepositoryMock->expects($this->once())->method('save')->with($contactMock);

        $this->model->execute($this->observerMock);
    }

    public function testExecuteLogsErrorOnValidationFailure()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData'])
            ->getMock();
        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $params = ['name' => 'Spammer', 'hideit' => 'I am a bot', 'email' => 'invalid', 'comment' => ''];

        $this->observerMock->method('getEvent')->willReturn($eventMock);
        $eventMock->method('getData')->with('request')->willReturn($requestMock);
        $requestMock->method('getParams')->willReturn($params);

        $this->loggerMock->expects($this->once())->method('error');

        $this->model->execute($this->observerMock);
    }
}
