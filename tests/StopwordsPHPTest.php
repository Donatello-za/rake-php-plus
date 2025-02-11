<?php

declare(strict_types=1);

use DonatelloZa\RakePlus\StopwordProviders\StopwordsPHP;
use PHPUnit\Framework\TestCase;

class StopwordsPHPTest extends TestCase
{
    public function testThrowsExceptionWhenNonExistingFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('file');

        StopwordsPHP::create('wrong');
    }

    public function testThrowsExceptionWhenIncorrectFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid results');

        StopwordsPHP::create(__DIR__ . '/fixtures/string_lang.php');
    }

    public function testThrowsExceptionWhenEmptyFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No words found');

        StopwordsPHP::create(__DIR__ . '/fixtures/empty_lang.php');
    }
}
