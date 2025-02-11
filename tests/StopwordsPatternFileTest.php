<?php

declare(strict_types=1);

use DonatelloZa\RakePlus\StopwordProviders\StopwordsPatternFile;
use PHPUnit\Framework\TestCase;

class StopwordsPatternFileTest extends TestCase
{
    public function testThrowsExceptionWhenNonExistingFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('file');

        StopwordsPatternFile::create('wrong');
    }
}
