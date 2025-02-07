<?php

declare(strict_types=1);

use DonatelloZa\RakePlus\StopwordsPHP;
use PHPUnit\Framework\TestCase;

class StopwordsPHPTest extends TestCase
{
    public function testThrowsExceptionWhenWrongFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('file');

        StopwordsPHP::create('wrong');
    }

    public function testThrowsExceptionWhenEmptyFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No words found');

        StopwordsPHP::create(__DIR__ . '/fixtures/empty_lang.php');
    }
}
