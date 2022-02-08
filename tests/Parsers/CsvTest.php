<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CsvTest extends TestCase
{
    public function testCheckCsv(): void
    {
        $stack = ['foo'];

        $this->assertSame('foo', $stack[count($stack)-1]);
        $this->assertSame(1, count($stack));

        $this->assertSame('foo', array_pop($stack));
        $this->assertSame(0, count($stack));
    }
}