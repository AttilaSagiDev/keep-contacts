<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Space\KeepContacts\Api\Data\ConfigInterface;

class ConfigTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    /**
     * @var Config
     */
    private Config $model;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Config($this->scopeConfigMock);
    }

    /**
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigInterface::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $this->assertTrue($this->model->isEnabled());
    }

    /**
     * @return void
     */
    public function testIsIncludeContactComment(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigInterface::XML_PATH_INCLUDE_ORIGINAL, ScopeInterface::SCOPE_STORE)
            ->willReturn(false);

        $this->assertFalse($this->model->isIncludeContactComment());
    }

    /**
     * @return void
     */
    public function testGetSenderEmail(): void
    {
        $expected = 'sender@example.com';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getSenderEmail());
    }

    /**
     * @return void
     */
    public function testGetCcEmail(): void
    {
        $expected = 'cc@example.com';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_CC_RECIPIENT, ScopeInterface::SCOPE_STORE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCcEmail());
    }

    /**
     * @return void
     */
    public function testGetEmailTemplate(): void
    {
        $expected = 'template_id';
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigInterface::XML_PATH_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailTemplate());
    }
}
