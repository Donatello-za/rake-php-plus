<?php

namespace DonatelloZa\RakePlus;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use stdClass;

class RakePlusTest extends PHPUnit_Framework_TestCase
{
    public static $mb_support = true;

    protected function setUp()
    {
        self::$mb_support = true;
    }

    public function testInstanceOf()
    {
        $rake = RakePlus::create("Hello World");
        $this->assertInstanceOf(RakePlus::class, $rake, 'RakePlus::create() returned invalid instance');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testEmptyLanguage()
    {
        RakePlus::create("Hello World", "");
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidLanguage()
    {
        RakePlus::create("Hello World", "blah");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNullLanguage()
    {
        RakePlus::create("Hello World", null);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidLangReturnStringFile()
    {
        RakePlus::create("Hello World", __DIR__ . '/test_string_lang.php');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidLangReturnEmptyArrayFile()
    {
        RakePlus::create("Hello World", __DIR__ . '/test_empty_array_lang.php');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testEmptyLanguageArray()
    {
        RakePlus::create("Hello World", []);
    }

    /**
     * @expectedException RuntimeException
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

    public function testArrayProvider()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordArray::create(['of', 'a', 'and', 'set', 'are', 'for']);
        RakePlus::create($text, $stopwords);
    }

    public function testNonMbPhrases()
    {
        self::$mb_support = false;

        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        RakePlus::create($text)->get();
    }

    public function testGetMinLength()
    {
        $rake = RakePlus::create("Hello World")->setMinLength(20);
        $this->assertEquals(20, $rake->getMinLength());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidMinLength()
    {
        RakePlus::create("Hello World")->setMinLength(-1);
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

    public function testPhrasesExtractAltPatternFile()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordsPatternFile::create('./tests/lang/en_US.ereg.pattern');
        $phrases = RakePlus::create($text, $stopwords)->get();

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

    public function testLoadStopwordLangPatternFile()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $stopwords = StopwordsPatternFile::create('./tests/lang/en_US.non_ereg.pattern');
        $phrases = RakePlus::create($text, $stopwords)->get();

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

    public function testArrayStopwordsNonMb()
    {
        self::$mb_support = false;

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

        $this->assertEquals(['of', 'a', 'and', 'set', 'are', 'for'], $stopwords->stopwords());
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

        $this->assertEquals(['of', 'a', 'and', 'set', 'are', 'for'], $stopwords->stopwords());
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

        $this->assertInternalType('array', $stopwords->stopwords());
        $this->assertGreaterThan(0, count($stopwords->stopwords()));
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

    public function testFilterNumerics()
    {
        $text = "6462 Little Crest Suite 413 Lake Carlietown, WA 12643";

        $rake = RakePlus::create($text, 'en_US', 0, false);
        $scores = $rake->scores();

        $this->assertEquals(false, $rake->getFilterNumerics());
        $this->assertCount(3, $scores);

        $this->assertEquals($scores['6462'], 0);
        $this->assertEquals($scores['wa 12643'], 1);
        $this->assertEquals($scores['crest suite 413 lake carlietown'], 16);
    }

    public function testDonNotFilterNumerics()
    {
        $text = "6462 Little Crest Suite 413 Lake Carlietown, WA 12643";
        $scores = RakePlus::create($text, 'en_US', 0, true)->scores();

        $this->assertCount(2, $scores);

        $this->assertEquals($scores['wa 12643'], 1);
        $this->assertEquals($scores['crest suite 413 lake carlietown'], 16);
    }

    public function testMinLengthScores()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $scores = RakePlus::create($text, 'en_US', 10)->sortByScore()->scores();

        $this->assertCount(11, $scores);

        $this->assertEquals($scores['compatibility'], 1);
        $this->assertEquals($scores['considered'], 1);
        $this->assertEquals($scores['components'], 1);
        $this->assertEquals($scores['algorithms'], 1);
        $this->assertEquals($scores['construction'], 1);
        $this->assertEquals($scores['strict inequations'], 4);
        $this->assertEquals($scores['nonstrict inequations'], 4);
        $this->assertEquals($scores['upper bounds'], 4);
        $this->assertEquals($scores['minimal set'], 4.5);
        $this->assertEquals($scores['minimal generating sets'], 8.5);
        $this->assertEquals($scores['linear diophantine equations'], 9);
    }

    public function testKeywords()
    {
        $text = "Criteria of compatibility of a system of linear Diophantine equations, " .
            "strict inequations, and nonstrict inequations are considered. Upper bounds " .
            "for components of a minimal set of solutions and algorithms of construction " .
            "of minimal generating sets of solutions for all types of systems are given.";

        $keywords = RakePlus::create($text)->keywords();

        $this->assertCount(22, $keywords);

        $this->assertContains('criteria', $keywords);
        $this->assertContains('compatibility', $keywords);
        $this->assertContains('system', $keywords);
        $this->assertContains('linear', $keywords);
        $this->assertContains('diophantine', $keywords);
        $this->assertContains('equations', $keywords);
        $this->assertContains('strict', $keywords);
        $this->assertContains('inequations', $keywords);
        $this->assertContains('nonstrict', $keywords);
        $this->assertContains('considered', $keywords);
        $this->assertContains('upper', $keywords);
        $this->assertContains('bounds', $keywords);
        $this->assertContains('components', $keywords);
        $this->assertContains('minimal', $keywords);
        $this->assertContains('set', $keywords);
        $this->assertContains('solutions', $keywords);
        $this->assertContains('algorithms', $keywords);
        $this->assertContains('construction', $keywords);
        $this->assertContains('generating', $keywords);
        $this->assertContains('sets', $keywords);
        $this->assertContains('types', $keywords);
        $this->assertContains('systems', $keywords);
    }

    public function testKeywordsWithNumbers()
    {
        $text = "6462 Little Crest Suite 413, Lake Carlietown, WA 12643";
        $keywords = RakePlus::create($text, 'en_US', 0, false)->keywords();

        $this->assertCount(8, $keywords);

        $this->assertContains('6462', $keywords);
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

    public function testKeywordsWithHyphens()
    {
        $text = "Because of the dominance of the Linux kernel-based Android OS on smartphones, Linux has the " .
            "largest installed base of all general-purpose operating systems.";
        $keywords = RakePlus::create($text, 'en_US', 0, false)->keywords();

        $this->assertCount(12, $keywords);

        $this->assertContains('dominance', $keywords);
        $this->assertContains('linux', $keywords);
        $this->assertContains('kernel-based', $keywords);
        $this->assertContains('android', $keywords);
        $this->assertContains('os', $keywords);
        $this->assertContains('smartphones', $keywords);
        $this->assertContains('largest', $keywords);
        $this->assertContains('installed', $keywords);
        $this->assertContains('general-purpose', $keywords);
        $this->assertContains('operating', $keywords);
        $this->assertContains('systems', $keywords);
    }

    public function testPhrasesWithHyphens()
    {
        $text = "Because of the dominance of the Linux kernel-based Android OS on smartphones, Linux has the " .
            "largest installed base of all general-purpose operating systems. More C-class articles can be read ".
            "on Wikipedia.";
        $phrases = RakePlus::create($text, 'en_US', 0, false)->get();

        $this->assertCount(9, $phrases);

        $this->assertContains('dominance', $phrases);
        $this->assertContains('linux kernel-based android os', $phrases);
        $this->assertContains('smartphones', $phrases);
        $this->assertContains('linux', $phrases);
        $this->assertContains('largest installed base', $phrases);
        $this->assertContains('general-purpose operating systems', $phrases);
        $this->assertContains('c-class articles', $phrases);
        $this->assertContains('read', $phrases);
        $this->assertContains('wikipedia', $phrases);
    }

    public function testPhrasesWithContractions()
    {
        $text = "It's of great importance that you're testing this properly. We'll make sure that there's no " .
            "could've, would've, should've situations this time around.";
        $phrases = RakePlus::create($text, 'en_US', 0, false)->get();

        $this->assertCount(6, $phrases);

        $this->assertContains('great importance', $phrases);
        $this->assertContains('testing', $phrases);
        $this->assertContains('properly', $phrases);
        $this->assertContains('make', $phrases);
        $this->assertContains('situations', $phrases);
        $this->assertContains('time', $phrases);
    }

    public function testWithOwnParseOptions()
    {
        $text = "It's of great importance that you're testing this properly. We'll make sure that there's no " .
            "could've, would've, should've situations this time around.";
        $phrases = RakePlus::create($text, 'en_US', 0, false, LangParseOptions::create())->get();

        $this->assertCount(6, $phrases);

        $this->assertContains('great importance', $phrases);
        $this->assertContains('testing', $phrases);
        $this->assertContains('properly', $phrases);
        $this->assertContains('make', $phrases);
        $this->assertContains('situations', $phrases);
        $this->assertContains('time', $phrases);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithInvalidParseOptions()
    {
        $text = "It's of great importance that you're testing this properly. We'll make sure that there's no " .
            "could've, would've, should've situations this time around.";
        RakePlus::create($text, 'en_US', 0, false, new stdClass())->get();
    }
}
