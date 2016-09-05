<?php

namespace Tests;

use DonatelloZa\RakePlus\RakePlus;
use DonatelloZa\RakePlus\StopwordArray;
use DonatelloZa\RakePlus\StopwordsPatternFile;
use DonatelloZa\RakePlus\StopwordsPHP;
use PHPUnit_Framework_TestCase;

class RakePlusTest extends PHPUnit_Framework_TestCase
{
    public function testInstanceOf()
    {
        $rake = RakePlus::create("Hello World");
        $this->assertInstanceOf(RakePlus::class, $rake, 'RakePlus::create() returned invalid instance');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyLanguage()
    {
        RakePlus::create("Hello World", "");
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidLanguage()
    {
        RakePlus::create("Hello World", "blah");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullLanguage()
    {
        RakePlus::create("Hello World", null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidLangReturnStringFile()
    {
        RakePlus::create("Hello World", __DIR__ . '/test_string_lang.php');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidLangReturnEmptyArrayFile()
    {
        RakePlus::create("Hello World", __DIR__ . '/test_empty_array_lang.php');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyLanguageArray()
    {
        RakePlus::create("Hello World", []);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNonExistingPatternFile()
    {
        RakePlus::create("Hello World", 'file_does_not_exist.pattern');
    }

    public function testCreateWithNoText()
    {
        $rake = new RakePlus();

        $phrases = $rake->get();
        $this->assertEmpty($phrases, "Phrases is supposed to be an empty array.");

        $scores = $rake->scores();
        $this->assertEmpty($scores, "Scores is supposed to be an empty array.");

        $sorted_phrases = $rake->sort()->get();
        $this->assertEmpty($sorted_phrases, "Sorted phrases is supposed to be an empty array.");

        $sorted_scores = $rake->sortByScore()->scores();
        $this->assertEmpty($sorted_scores, "Sorted scores is supposed to be an empty array.");
    }

    public function testSortInstance()
    {
        $rake = RakePlus::create("Hello World")->sort();
        $this->assertInstanceOf(RakePlus::class, $rake, 'RakePlus::create()->sort() returned invalid instance');
    }

    public function testSortByScoreInstance()
    {
        $rake = RakePlus::create("Hello World")->sortByScore();
        $this->assertInstanceOf(RakePlus::class, $rake, 'RakePlus::create()->sortByScore() returned invalid instance');
    }

    public function testPhrasesGetType()
    {
        $phrases = RakePlus::create("Hello World")->get();
        $this->assertInternalType('array', $phrases, 'RakePlus::create()->get() array expected');
    }

    public function testScoresGetType()
    {
        $scores = RakePlus::create("Hello World")->scores();
        $this->assertInternalType('array', $scores, 'RakePlus::create()->scores() array expected');
    }

    public function testLanguage()
    {
        $language = RakePlus::create("Hello World")->language();
        $this->assertEquals("en_US", $language);
    }

    public function testLanguageFile()
    {
        $language_file = RakePlus::create("Hello World")->languageFile();
        $this->assertContains("/lang/en_US.pattern", $language_file);
    }

    public function testPhrasesExtract()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $phrases = RakePlus::create($text)->get();

        $this->assertContains('algorithms', $phrases);
        $this->assertContains('compatibility', $phrases);
        $this->assertContains('components', $phrases);
        $this->assertContains('considered', $phrases);
        $this->assertContains('construction', $phrases);
        $this->assertContains('criteria', $phrases);
        $this->assertContains('linear diophantine equations', $phrases);
        $this->assertContains('minimal generating sets', $phrases);
        $this->assertContains('minimal set', $phrases);
        $this->assertContains('nonstrict inequations', $phrases);
        $this->assertContains('solutions', $phrases);
        $this->assertContains('strict inequations', $phrases);
        $this->assertContains('system', $phrases);
        $this->assertContains('systems', $phrases);
        $this->assertContains('types', $phrases);
        $this->assertContains('upper bounds', $phrases);
    }

    public function testPhrasesSortedKeys()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $phrases = RakePlus::create($text)->sort()->get();

        $this->assertCount(16, $phrases);

        for ($i = 0; $i < 16; $i++) {
            $this->assertArrayHasKey(0, $phrases);
        }
    }

    public function testPhrasesSortedValuesAsc()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $phrases = RakePlus::create($text)->sort()->get();

        $this->assertCount(16, $phrases);

        $this->assertEquals('algorithms', $phrases[0]);
        $this->assertEquals('compatibility', $phrases[1]);
        $this->assertEquals('components', $phrases[2]);
        $this->assertEquals('considered', $phrases[3]);
        $this->assertEquals('construction', $phrases[4]);
        $this->assertEquals('criteria', $phrases[5]);
        $this->assertEquals('linear diophantine equations', $phrases[6]);
        $this->assertEquals('minimal generating sets', $phrases[7]);
        $this->assertEquals('minimal set', $phrases[8]);
        $this->assertEquals('nonstrict inequations', $phrases[9]);
        $this->assertEquals('solutions', $phrases[10]);
        $this->assertEquals('strict inequations', $phrases[11]);
        $this->assertEquals('system', $phrases[12]);
        $this->assertEquals('systems', $phrases[13]);
        $this->assertEquals('types', $phrases[14]);
        $this->assertEquals('upper bounds', $phrases[15]);
    }

    public function testPhrasesSortedValuesDesc()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $phrases = RakePlus::create($text)->sort('desc')->get();

        $this->assertCount(16, $phrases);

        $this->assertEquals('algorithms', $phrases[15]);
        $this->assertEquals('compatibility', $phrases[14]);
        $this->assertEquals('components', $phrases[13]);
        $this->assertEquals('considered', $phrases[12]);
        $this->assertEquals('construction', $phrases[11]);
        $this->assertEquals('criteria', $phrases[10]);
        $this->assertEquals('linear diophantine equations', $phrases[9]);
        $this->assertEquals('minimal generating sets', $phrases[8]);
        $this->assertEquals('minimal set', $phrases[7]);
        $this->assertEquals('nonstrict inequations', $phrases[6]);
        $this->assertEquals('solutions', $phrases[5]);
        $this->assertEquals('strict inequations', $phrases[4]);
        $this->assertEquals('system', $phrases[3]);
        $this->assertEquals('systems', $phrases[2]);
        $this->assertEquals('types', $phrases[1]);
        $this->assertEquals('upper bounds', $phrases[0]);
    }

    public function testScores()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text)->scores();

        $this->assertCount(16, $scores);

        $this->assertArrayHasKey('algorithms', $scores);
        $this->assertArrayHasKey('compatibility', $scores);
        $this->assertArrayHasKey('components', $scores);
        $this->assertArrayHasKey('considered', $scores);
        $this->assertArrayHasKey('construction', $scores);
        $this->assertArrayHasKey('criteria', $scores);
        $this->assertArrayHasKey('linear diophantine equations', $scores);
        $this->assertArrayHasKey('minimal generating sets', $scores);
        $this->assertArrayHasKey('minimal set', $scores);
        $this->assertArrayHasKey('nonstrict inequations', $scores);
        $this->assertArrayHasKey('solutions', $scores);
        $this->assertArrayHasKey('strict inequations', $scores);
        $this->assertArrayHasKey('system', $scores);
        $this->assertArrayHasKey('systems', $scores);
        $this->assertArrayHasKey('types', $scores);
        $this->assertArrayHasKey('upper bounds', $scores);
    }

    public function testScoreValuesAsc()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text)->sortByScore()->scores();

        $this->assertCount(16, $scores);

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['types'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testScoreValuesDesc()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text)->sortByScore('desc')->scores();

        $this->assertCount(16, $scores);

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['types'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testArrayStopwords()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text, ['of', 'a', 'and', 'set', 'are', 'for'])->sortByScore()->scores();

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['given'], 1);
        $this->assertEquals($scores['minimal'], 2);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['all types'], 4);
        $this->assertEquals($scores['minimal generating sets'], 8);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testPHPStopwords()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text, __DIR__ . '/test_en_US.php')->sortByScore()->scores();

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['types'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testStopWordArrayInstance()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordArray::create(['of', 'a', 'and', 'set', 'are', 'for']);
        $scores = RakePlus::create($text, $stopwords)->sortByScore()->scores();

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['given'], 1);
        $this->assertEquals($scores['minimal'], 2);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['all types'], 4);
        $this->assertEquals($scores['minimal generating sets'], 8);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testStopWordPHPInstance()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordsPHP::createFromLanguage('en_US');
        $scores = RakePlus::create($text, $stopwords)->sortByScore()->scores();

        $this->assertContains("/lang/en_US.php", $stopwords->filename());

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['types'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testStopWordPatternFileInstance()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordsPatternFile::createFromLanguage('en_US');
        $scores = RakePlus::create($text, $stopwords)->sortByScore()->scores();

        $this->assertContains("/lang/en_US.pattern", $stopwords->filename());

        $this->assertEquals($scores['criteria'], 1);
        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['system'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['solutions'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['types'], 1);
        $this->assertEquals($scores['systems'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }
}
