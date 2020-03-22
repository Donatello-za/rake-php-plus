<?php

namespace DonatelloZa\RakePlus;

use PHPUnit_Framework_TestCase;

class lang_es_AR_Test extends PHPUnit_Framework_TestCase
{
    public static $mb_support = true;

    protected function setUp()
    {
        self::$mb_support = true;
    }

    public function testPhrases()
    {
        $text = "Saritha está viendo una película de Bollywood con su novio Chris. " .
            "Las películas de Bollywood son filmadas en lengua hindi por la industria " .
            "cinematográfica con sede en Mumbai, India. La 'B' de Bollywood viene de 'Bombay', " .
            "el antiguo nombre de Mumbai. Las películas tradicionales de Bollywood no tienen " .
            "la intención de ser realistas.";

        $phrases = RakePlus::create($text, 'es_AR')->get();

        $this->assertCount(19, $phrases);

        $this->assertContains('saritha', $phrases);
        $this->assertContains('viendo', $phrases);
        $this->assertContains('película', $phrases);
        $this->assertContains('bollywood', $phrases);
        $this->assertContains('novio chris', $phrases);
        $this->assertContains('películas', $phrases);
        $this->assertContains('filmadas', $phrases);
        $this->assertContains('lengua hindi', $phrases);
        $this->assertContains('industria cinematográfica', $phrases);
        $this->assertContains('sede', $phrases);
        $this->assertContains('mumbai', $phrases);
        $this->assertContains('india', $phrases);
        $this->assertContains('\'b\'', $phrases);
        $this->assertContains('bollywood viene', $phrases);
        $this->assertContains('\'bombay\'', $phrases);
        $this->assertContains('antiguo nombre', $phrases);
        $this->assertContains('películas tradicionales', $phrases);
        $this->assertContains('intención', $phrases);
        $this->assertContains('realistas', $phrases);
    }

    public function testKeywordsMinimumLength()
    {
        $text = "Saritha está viendo una película de Bollywood con su novio Chris. " .
            "Las películas de Bollywood son filmadas en lengua hindi por la industria " .
            "cinematográfica con sede en Mumbai, India. La 'B' de Bollywood viene de 'Bombay', " .
            "el antiguo nombre de Mumbai. Las películas tradicionales de Bollywood no tienen " .
            "la intención de ser realistas.";

        $rake = RakePlus::create($text, 'es_AR', 8, false);
        $keywords = $rake->sortByScore('desc')->keywords();
        $this->assertCount(10, $keywords);

        $this->assertContains('industria', $keywords);
        $this->assertContains('cinematográfica', $keywords);
        $this->assertContains('películas', $keywords);
        $this->assertContains('tradicionales', $keywords);
        $this->assertContains('bollywood', $keywords);
        $this->assertContains('película', $keywords);
        $this->assertContains('filmadas', $keywords);
        $this->assertContains('\'bombay\'', $keywords);
        $this->assertContains('intención', $keywords);
        $this->assertContains('realistas', $keywords);
    }

    public function testKeywordsWithNumbers()
    {
        $text = "6462 Little Crest Suite 413, Lake Carlietown, WA 12643";
        $keywords = RakePlus::create($text, 'es_AR', 0, false)->keywords();

        $this->assertCount(9, $keywords);

        $this->assertContains('6462', $keywords);
        $this->assertContains('little', $keywords);
        $this->assertContains('crest', $keywords);
        $this->assertContains('suite', $keywords);
        $this->assertContains('lake', $keywords);
        $this->assertContains('carlietown', $keywords);
        $this->assertContains('wa', $keywords);
        $this->assertContains('12643', $keywords);

        foreach ($keywords as $keyword) {
            $this->assertInternalType('string', $keyword);
        }
    }

    public function testNumberedKeywordLimitedLengths()
    {
        $text = "6462 Little Crest Suite 413, Lake Carlietown, WA 12643";
        $keywords = RakePlus::create($text, 'es_AR', 3, true)->keywords();

        $this->assertCount(5, $keywords);

        $this->assertContains('little', $keywords);
        $this->assertContains('crest', $keywords);
        $this->assertContains('suite', $keywords);
        $this->assertContains('lake', $keywords);
        $this->assertContains('carlietown', $keywords);
    }
}
