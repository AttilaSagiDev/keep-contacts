<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Ui\Component\Listing\Column\ContactActions;

class ContactActionsTest extends TestCase
{
    /**
     * @var ContactActions
     */
    private ContactActions $column;

    /**
     * @var MockObject|UrlInterface
     */
    private MockObject|UrlInterface $urlBuilderMock;

    /**
     * @var MockObject|Escaper
     */
    private MockObject|Escaper $escaperMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->escaperMock = $this->createMock(Escaper::class);
        $contextMock = $this->getMockForAbstractClass(ContextInterface::class);
        $uiComponentFactoryMock = $this->createMock(UiComponentFactory::class);

        $this->column = $objectManager->getObject(
            ContactActions::class,
            [
                'context' => $contextMock,
                'uiComponentFactory' => $uiComponentFactoryMock,
                'urlBuilder' => $this->urlBuilderMock,
                'escaper' => $this->escaperMock,
                'data' => ['name' => 'actions']
            ]
        );
    }

    public function testPrepareDataSource()
    {
        $contactId = 123;
        $contactName = 'Attila Sagi';
        $editUrl = 'http://example.com/keep_contacts/contacts/edit/contact_id/123';
        $deleteUrl = 'http://example.com/keep_contacts/contacts/delete/contact_id/123';

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'contact_id' => $contactId,
                        'name' => $contactName
                    ]
                ]
            ]
        ];

        $this->escaperMock->expects($this->once())
            ->method('escapeHtmlAttr')
            ->with($contactName)
            ->willReturn($contactName);

        $this->urlBuilderMock->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturnMap([
                [ContactActions::URL_PATH_EDIT, ['contact_id' => $contactId], $editUrl],
                [ContactActions::URL_PATH_DELETE, ['contact_id' => $contactId], $deleteUrl],
            ]);

        $result = $this->column->prepareDataSource($dataSource);

        $actions = $result['data']['items'][0]['actions'];

        $this->assertArrayHasKey('edit', $actions);
        $this->assertEquals($editUrl, $actions['edit']['href']);
        $this->assertEquals('Edit / Answer', (string)$actions['edit']['label']);

        $this->assertArrayHasKey('delete', $actions);
        $this->assertEquals($deleteUrl, $actions['delete']['href']);
        $this->assertEquals('Delete', (string)$actions['delete']['label']);
        $this->assertTrue($actions['delete']['post']);

        $this->assertArrayHasKey('confirm', $actions['delete']);
        $this->assertEquals("Delete $contactName", (string)$actions['delete']['confirm']['title']);
    }

    public function testPrepareDataSourceWithoutContactId()
    {
        $dataSource = [
            'data' => [
                'items' => [
                    ['name' => 'Ghost Contact']
                ]
            ]
        ];

        $result = $this->column->prepareDataSource($dataSource);

        $this->assertArrayNotHasKey('actions', $result['data']['items'][0]);
    }
}
