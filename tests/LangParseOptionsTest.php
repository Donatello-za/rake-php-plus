<?php declare(strict_types=1);

use DonatelloZa\RakePlus\LangParseOptions;
use PHPUnit\Framework\TestCase;


class LangParseOptionsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testNoLanguageConstruct()
    {
        $options = LangParseOptions::create();
        $this->assertEquals('en_US', $options->getLanguage());
        $this->assertEquals('[.!?,;:\t\"\(\)]', $options->getSentenceRegex());
        $this->assertEquals("\n", $options->getLineTerminator());
    }

    public function testUnknownLanguageConstruct()
    {
        $options = LangParseOptions::create('en_Whatever');
        $this->assertEquals('en_Whatever', $options->getLanguage());
        $this->assertEquals('[.!?,;:\t\"\(\)]', $options->getSentenceRegex());
        $this->assertEquals("\n", $options->getLineTerminator());
    }

    public function test_en_USLanguageConstruct()
    {
        $options = LangParseOptions::create('en_US');
        $this->assertEquals('en_US', $options->getLanguage());
        $this->assertEquals('[.!?,;:\t\"\(\)]', $options->getSentenceRegex());
        $this->assertEquals("\n", $options->getLineTerminator());
    }

    public function test_ar_AE_LanguageConstruct()
    {
        $options = LangParseOptions::create('ar_AE');
        $this->assertEquals('ar_AE', $options->getLanguage());
        $this->assertEquals('[-؛؟،“.!?,;:\t\"\(\)]', $options->getSentenceRegex());
        $this->assertEquals("\n", $options->getLineTerminator());
    }

    public function test_ckb_IQ_LanguageConstruct()
    {
        $options = LangParseOptions::create('ckb_IQ');
        $this->assertEquals('ckb_IQ', $options->getLanguage());
        $this->assertEquals('[-؛؟،“.!?,;:\t\"\(\)]', $options->getSentenceRegex());
        $this->assertEquals("\n", $options->getLineTerminator());
    }
}
