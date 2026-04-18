<?php
/**
 * Copyright (c) 2026 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\KeepContacts\Test\Unit\Model\Source;

use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;
use Space\KeepContacts\Model\Source\IsAnswered;

class IsAnsweredTest extends TestCase
{
    /**
     * @var IsAnswered
     */
    private IsAnswered $model;

    protected function setUp(): void
    {
        $this->model = new IsAnswered();
    }

    public function testToOptionArray()
    {
        $result = $this->model->toOptionArray();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertEquals(IsAnswered::IS_ANSWERED_NO, $result[0]['value']);
        $this->assertInstanceOf(Phrase::class, $result[0]['label']);
        $this->assertEquals('No', (string)$result[0]['label']);

        $this->assertEquals(IsAnswered::IS_ANSWERED_YES, $result[1]['value']);
        $this->assertInstanceOf(Phrase::class, $result[1]['label']);
        $this->assertEquals('Yes', (string)$result[1]['label']);
    }

    public function testGetAvailableAnswers()
    {
        $result = $this->model->getAvailableAnswers();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(IsAnswered::IS_ANSWERED_NO, $result);
        $this->assertArrayHasKey(IsAnswered::IS_ANSWERED_YES, $result);

        $this->assertEquals('No', (string)$result[IsAnswered::IS_ANSWERED_NO]);
        $this->assertEquals('Yes', (string)$result[IsAnswered::IS_ANSWERED_YES]);
    }
}
