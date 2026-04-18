<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\Data\ConfigInterface;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Model\Service\SendMail;

class SendMailTest extends TestCase
{
    /**
     * @var TransportBuilder|MockObject
     */
    private TransportBuilder|MockObject $transportBuilderMock;

    /**
     * @var ConfigInterface|MockObject
     */
    private ConfigInterface|MockObject $configMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private StoreManagerInterface|MockObject $storeManagerMock;

    /**
     * @var SendMail
     */
    private SendMail $model;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->transportBuilderMock = $this->getMockBuilder(TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMock();

        $this->model = new SendMail(
            $this->transportBuilderMock,
            $this->configMock,
            $this->storeManagerMock
        );
    }

    public function testSendKeepContactsEmail(): void
    {
        $contactMock = $this->getMockBuilder(ContactInterface::class)->getMock();
        $contactMock->method('getAnswer')->willReturn('The answer');
        $contactMock->method('getStoreId')->willReturn(1);
        $contactMock->method('getName')->willReturn('John Doe');
        $contactMock->method('getEmail')->willReturn('john@example.com');
        $contactMock->method('getTelephone')->willReturn('123456789');
        $contactMock->method('getComment')->willReturn('The comment');

        $this->configMock->method('getCcEmail')->willReturn('cc@example.com');
        $this->configMock->method('getEmailTemplate')->willReturn('template_id');
        $this->configMock->method('isIncludeContactComment')->willReturn(true);
        $this->configMock->method('getSenderEmail')->willReturn('sender@example.com');

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->method('getId')->willReturn(1);

        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $transportMock = $this->getMockBuilder(TransportInterface::class)->getMock();
        $transportMock->expects($this->once())->method('sendMessage');

        $this->transportBuilderMock->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilderMock->method('setReplyTo')->willReturnSelf();
        $this->transportBuilderMock->method('setFromByScope')->willReturnSelf();
        $this->transportBuilderMock->method('addTo')->willReturnSelf();
        $this->transportBuilderMock->method('addCc')->willReturnSelf();
        $this->transportBuilderMock->method('getTransport')->willReturn($transportMock);

        $this->model->sendKeepContactsEmail($contactMock);
    }

    public function testSendKeepContactsEmailThrowsExceptionWhenAnswerIsEmpty(): void
    {
        $contactMock = $this->getMockBuilder(ContactInterface::class)->getMock();
        $contactMock->method('getAnswer')->willReturn('');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Answer is empty');

        $this->model->sendKeepContactsEmail($contactMock);
    }
}
