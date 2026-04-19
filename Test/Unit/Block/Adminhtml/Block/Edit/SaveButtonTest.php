<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Block\Adminhtml\Block\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\Control\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Space\KeepContacts\Api\ContactRepositoryInterface;
use Space\KeepContacts\Block\Adminhtml\Block\Edit\SaveButton;

class SaveButtonTest extends TestCase
{
    /**
     * @var SaveButton
     */
    private SaveButton $button;

    /**
     * @var MockObject|Context
     */
    private MockObject|Context $contextMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $contactRepositoryMock = $this->createMock(ContactRepositoryInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->button = $objectManager->getObject(
            SaveButton::class,
            [
                'context' => $this->contextMock,
                'contactRepository' => $contactRepositoryMock,
                'logger' => $loggerMock
            ]
        );
    }

    public function testGetButtonData()
    {
        $result = $this->button->getButtonData();

        $this->assertIsArray($result);
        $this->assertEquals('save primary', $result['class']);
        $this->assertEquals(Container::SPLIT_BUTTON, $result['class_name']);

        $this->assertEquals('Save & Answer', (string)$result['label']);
        $this->assertEquals('Save options', (string)$result['dropdown_button_aria_label']);

        $actions = $result['data_attribute']['mage-init']['buttonAdapter']['actions'];
        $this->assertEquals('keep_contacts_form.keep_contacts_form', $actions[0]['targetName']);
        $this->assertEquals('continue', $actions[0]['params'][1]['back']);

        $this->assertCount(1, $result['options']);
        $option = $result['options'][0];

        $this->assertEquals('save_and_close', $option['id_hard']);
        $this->assertEquals('Save Only And Close', (string)$option['label']);

        $optionActions = $option['data_attribute']['mage-init']['buttonAdapter']['actions'];
        $this->assertEquals('close', $optionActions[0]['params'][1]['back']);
    }
}
