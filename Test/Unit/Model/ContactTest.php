<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Api\Data\ContactInterface;
use Space\KeepContacts\Model\Contact;

class ContactTest extends TestCase
{
    /**
     * @var Contact
     */
    private Contact $model;

    /**
     * @var Context|MockObject
     */
    private Context|MockObject $contextMock;

    /**
     * @var Registry|MockObject
     */
    private Registry|MockObject $registryMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->registryMock = $this->createMock(Registry::class);

        $objectManagerHelper = new ObjectManagerHelper($this);

        $this->model = $objectManagerHelper->getObject(
            Contact::class,
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock
            ]
        );
    }

    /**
     * Test getIdentities method
     */
    public function testGetIdentities(): void
    {
        $id = 123;
        $this->model->setData(ContactInterface::CONTACT_ID, $id);

        $expected = ['keep_contacts_123', 'keep_contacts_123'];
        $this->assertEquals($expected, $this->model->getIdentities());
    }

    /**
     * @dataProvider getterSetterDataProvider
     */
    public function testGettersAndSetters(string $methodSuffix, string $dataKey, $value, $expectedValue): void
    {
        $getter = 'get' . $methodSuffix;
        $setter = 'set' . $methodSuffix;

        if (!method_exists($this->model, $getter) && method_exists($this->model, 'is' . $methodSuffix)) {
            $getter = 'is' . $methodSuffix;
        }

        $result = $this->model->$setter($value);
        $this->assertSame($this->model, $result);

        $this->assertEquals($expectedValue, $this->model->getData($dataKey));
        $this->assertEquals($expectedValue, $this->model->$getter());
    }

    /**
     * Getter and setter data provider
     */
    public static function getterSetterDataProvider(): array
    {
        return [
            ['Id', ContactInterface::CONTACT_ID, 10, 10],
            ['Name', ContactInterface::NAME, 'John Doe', 'John Doe'],
            ['Email', ContactInterface::EMAIL, 'test@example.com', 'test@example.com'],
            ['Telephone', ContactInterface::TELEPHONE, '123456789', '123456789'],
            ['Comment', ContactInterface::COMMENT, 'Hello world', 'Hello world'],
            ['Answer', ContactInterface::ANSWER, 'Sample answer', 'Sample answer'],
            ['CreationTime', ContactInterface::CREATION_TIME, '2024-01-01 00:00:00', '2024-01-01 00:00:00'],
            ['UpdateTime', ContactInterface::UPDATE_TIME, '2024-01-02 00:00:00', '2024-01-02 00:00:00'],
            ['IsAnswered', ContactInterface::IS_ANSWERED, true, true],
            ['StoreId', ContactInterface::STORE_ID, 1, 1],
        ];
    }
}
