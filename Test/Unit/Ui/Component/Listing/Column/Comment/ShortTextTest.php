<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Ui\Component\Listing\Column\Comment;

use Magento\Framework\Filter\FilterManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Ui\Component\Listing\Column\Comment\ShortText;

class ShortTextTest extends TestCase
{
    /**
     * @var ShortText
     */
    private ShortText $shortText;

    /**
     * @var MockObject|FilterManager
     */
    private MockObject|FilterManager $filterManagerMock;

    /**
     * @var MockObject|ContextInterface
     */
    private MockObject|ContextInterface $contextMock;

    /**
     * @var MockObject|UiComponentFactory
     */
    private MockObject|UiComponentFactory $uiComponentFactoryMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->filterManagerMock = $this->getMockBuilder(FilterManager::class)
            ->disableOriginalConstructor()
            ->addMethods(['truncate'])
            ->getMock();

        $this->contextMock = $this->getMockForAbstractClass(ContextInterface::class);
        $this->uiComponentFactoryMock = $this->createMock(UiComponentFactory::class);

        $this->shortText = $objectManager->getObject(
            ShortText::class,
            [
                'context' => $this->contextMock,
                'uiComponentFactory' => $this->uiComponentFactoryMock,
                'filterManager' => $this->filterManagerMock,
                'data' => ['name' => 'comment']
            ]
        );
    }

    public function testPrepareDataSource()
    {
        $fieldName = 'comment';
        $originalText = 'A long string of text here.';
        $truncatedText = 'A long...';

        $dataSource = [
            'data' => [
                'items' => [
                    [$fieldName => $originalText]
                ]
            ]
        ];

        $this->filterManagerMock->expects($this->once())
            ->method('truncate')
            ->with($originalText, ['length' => 255, 'etc' => '...'])
            ->willReturn($truncatedText);

        $result = $this->shortText->prepareDataSource($dataSource);

        $this->assertEquals($truncatedText, $result['data']['items'][0][$fieldName]);
    }

    public function testPrepareDataSourceWithEmptyItems()
    {
        $dataSource = ['data' => []];
        $this->filterManagerMock->expects($this->never())->method('truncate');

        $result = $this->shortText->prepareDataSource($dataSource);

        $this->assertEquals($dataSource, $result);
    }
}
