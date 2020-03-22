<?php

namespace DonatelloZa\RakePlus;

use PHPUnit_Framework_TestCase;

class lang_ckb_IQ_Test extends PHPUnit_Framework_TestCase
{
    public static $mb_support = true;

    protected function setUp()
    {
        self::$mb_support = true;
    }

    public function testGeneralKurdishPhrase()
    {
        $text = "كوردێكی دانیشتوی فینلاند، تابلۆیه‌كی به‌ ناوی \"ڤایرۆسی كۆرۆنا\" كێشا، ئێسته‌ له‌ یه‌كێك له‌ به‌ناوبانگترین ماڵپه‌ڕه‌كانی فرۆشتنی تابلۆی ئۆنلاین، خستویه‌تییه‌ڕو بۆ فرۆشتن.";
        $phrases = RakePlus::create($text, 'ckb_IQ', 0, false)->get();

        $this->assertCount(7, $phrases);
        $this->assertContains('كوردێكی دانیشتوی فینلاند', $phrases);
        $this->assertContains('تابلۆیه‌كی به‌ ناوی', $phrases);
        $this->assertContains('ڤایرۆسی كۆرۆنا', $phrases);
        $this->assertContains('كێشا', $phrases);
        $this->assertContains('ئێسته‌ له‌ یه‌كێك له‌ به‌ناوبانگترین ماڵپه‌ڕه‌كانی فرۆشتنی تابلۆی ئۆنلاین', $phrases);
        $this->assertContains('خستویه‌تییه‌ڕو', $phrases);
        $this->assertContains('فرۆشتن', $phrases);
    }

    public function testGeneralKurdishKeywords()
    {
        $text = "كوردێكی دانیشتوی فینلاند، تابلۆیه‌كی به‌ ناوی \"ڤایرۆسی كۆرۆنا\" كێشا، ئێسته‌ له‌ یه‌كێك له‌ به‌ناوبانگترین ماڵپه‌ڕه‌كانی فرۆشتنی تابلۆی ئۆنلاین، خستویه‌تییه‌ڕو بۆ فرۆشتن.";
        $keywords = RakePlus::create($text, 'ckb_IQ', 0, false)->keywords();

        $this->assertCount(19, $keywords);
        $this->assertContains('كوردێكی', $keywords);
        $this->assertContains('دانیشتوی', $keywords);
        $this->assertContains('فینلاند', $keywords);
        $this->assertContains('تابلۆیه‌كی', $keywords);
        $this->assertContains('به‌', $keywords);
        $this->assertContains('ناوی', $keywords);
        $this->assertContains('ڤایرۆسی', $keywords);
        $this->assertContains('كۆرۆنا', $keywords);
        $this->assertContains('كێشا', $keywords);
        $this->assertContains('ئێسته‌', $keywords);
        $this->assertContains('له‌', $keywords);
        $this->assertContains('یه‌كێك', $keywords);
        $this->assertContains('به‌ناوبانگترین', $keywords);
        $this->assertContains('ماڵپه‌ڕه‌كانی', $keywords);
        $this->assertContains('فرۆشتنی', $keywords);
        $this->assertContains('تابلۆی', $keywords);
        $this->assertContains('ئۆنلاین', $keywords);
        $this->assertContains('خستویه‌تییه‌ڕو', $keywords);
        $this->assertContains('فرۆشتن', $keywords);
    }
}
