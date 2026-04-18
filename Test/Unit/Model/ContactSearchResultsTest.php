<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Model\ContactSearchResults;
use Space\KeepContacts\Api\Data\ContactInterface;

class ContactSearchResultsTest extends TestCase
{
    /**
     * @var ContactSearchResults
     */
    private ContactSearchResults $model;

    protected function setUp(): void
    {
        $this->model = new ContactSearchResults();
    }

    public function testSetAndGetItems()
    {
        $contactMock = $this->getMockBuilder(ContactInterface::class)
            ->getMockForAbstractClass();

        $items = [$contactMock];

        $this->model->setItems($items);
        $this->assertEquals($items, $this->model->getItems());
    }

    public function testSetAndGetTotalCount()
    {
        $totalCount = 5;
        $this->model->setTotalCount($totalCount);
        $this->assertEquals($totalCount, $this->model->getTotalCount());
    }
}
