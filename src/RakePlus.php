<?php

namespace DonatelloZa\RakePlus;

use InvalidArgumentException;

class RakePlus
{
    /** @var string */
    protected $language = 'en_US';

    /** @var string */
    protected $language_file = "";

    /** @var string|null */
    private $pattern = null;

    /** @var array */
    private $phrase_scores = [];

    /** @var int */
    private $min_length = 0;

    /** @var bool */
    private $filter_numerics = true;

    /** @var string */
    private $sentence_regex;

    /** @var string */
    private $line_terminator;

    /** @var bool */
    public $mb_support = false;

    /** @var LangParseOptions */
    public $parseOptions;

    const ORDER_ASC = 'asc';

    const ORDER_DESC = 'desc';

    /**
     * RakePlus constructor. Instantiates RakePlus and extracts
     * the key phrases from the text if supplied.
     *
     * If $stopwords is a string the method will:
     *
     * 1) Determine if it is has a .pattern or .php extension and if
     *    so will attempt to load the stopwords from the specified path
     *    and filename.
     * 2) If it does not have a .pattern or .php extension, it will assume
     *    that a language string was specified and will then attempt to
     *    read the stopwords from lang/xxxx.pattern or lang/xxxx.php, where
     *    xxxx is the language string (default: en_US)
     *
     * If $stopwords os an array it will simply use the array of stopwords
     * as provided.
     *
     * If $stopwords is a derived instance of StopwordAbstract it will simply
     * retrieve the stopwords from the instance.
     *
     * @param string|null                           $text              Text to turn into keywords/phrases.
     * @param AbstractStopwordProvider|string|array $stopwords         Stopwords/language to use.
     * @param int                                   $phrase_min_length Minimum keyword/phrase length.
     * @param bool                                  $filter_numerics   Filter out numeric numbers.
     * @param null|LangParseOptions                 $parseOptions      Additional text parsing options, see:
     *                                                                 @LangParseOptions
     */
    public function __construct($text = null, $stopwords = 'en_US', $phrase_min_length = 0, $filter_numerics = true,
                                $parseOptions = null)
    {
        $this->mb_support = extension_loaded('mbstring');

        $this->setMinLength($phrase_min_length);
        $this->setFilterNumerics($filter_numerics);

        if ($parseOptions === null) {
            $this->parseOptions = LangParseOptions::create($stopwords);
        } else if ($parseOptions instanceof ILangParseOptions) {
            $this->parseOptions = $parseOptions;
        } else {
            throw new InvalidArgumentException("The \$parseOptions argument must be an instance of ILangParseOptions.");
        }

        $this->sentence_regex = $this->parseOptions->getSentenceRegex();
        $this->line_terminator = $this->parseOptions->getLineTerminator();

        if (!is_null($text)) {
            $this->extract($text, $stopwords);
        }
    }

    /**
     * Instantiates a RakePlus instance and extracts
     * the key phrases from the text.
     *
     * If $stopwords is a string the method will:
     *
     * 1) Determine if it is has a .pattern or .php extension and if
     *    so will attempt to load the stopwords from the specified path
     *    and filename.
     * 2) If it does not have a .pattern or .php extension, it will assume
     *    that a language string was specified and will then attempt to
     *    read the stopwords from lang/xxxx.pattern or lang/xxxx.php, where
     *    xxxx is the language string (default: en_US)
     *
     * If $stopwords os an array it will simply use the array of stopwords
     * as provided.
     *
     * If $stopwords is a derived instance of StopwordAbstract it will simply
     * retrieve the stopwords from the instance.
     *
     * @param string|null                           $text              Text to turn into keywords/phrases.
     * @param AbstractStopwordProvider|string|array $stopwords         Stopwords to use.
     * @param int                                   $phrase_min_length Minimum keyword/phrase length.
     * @param bool                                  $filter_numerics   Filter out numeric numbers.
     * @param null|LangParseOptions                 $parseOptions      Additional text parsing options, see:
     *                                                                 @LangParseOptions
     *
     * @return RakePlus
     */
    public static function create($text, $stopwords = 'en_US', $phrase_min_length = 0, $filter_numerics = true,
                                  $parseOptions = null)
    {
        return (new self($text, $stopwords, $phrase_min_length, $filter_numerics, $parseOptions));
    }

    /**
     * Extracts the key phrases from the text.
     *
     * If $stopwords is a string the method will:
     *
     * 1) Determine if it is has a .pattern or .php extension and if
     *    so will attempt to load the stopwords from the specified path
     *    and filename.
     * 2) If it does not have a .pattern or .php extension, it will assume
     *    that a language string was specified and will then attempt to
     *    read the stopwords from lang/xxxx.pattern or lang/xxxx.php, where
     *    xxxx is the language string (default: en_US)
     *
     * If $stopwords os an array it will simply use the array of stopwords
     * as provided.
     *
     * If $stopwords is a derived instance of StopwordAbstract it will simply
     * retrieve the stopwords from the instance.
     *
     * @param string                                $text
     * @param AbstractStopwordProvider|string|array $stopwords
     *
     * @return RakePlus
     */
    public function extract($text, $stopwords = 'en_US')
    {
        if ($text != '') {
            if (is_array($stopwords)) {
                $this->pattern = StopwordArray::create($stopwords)->pattern();
            } else if (is_string($stopwords)) {
                if (is_null($this->pattern) || ($this->language != $stopwords)) {
                    $extension = mb_strtolower(pathinfo($stopwords, PATHINFO_EXTENSION));
                    if (empty($extension)) {
                        // First try the .pattern file
                        $this->language_file = StopwordsPatternFile::languageFile($stopwords);
                        if (file_exists($this->language_file)) {
                            $this->pattern = StopwordsPatternFile::create($this->language_file)->pattern();
                        } else {
                            $this->language_file = StopwordsPHP::languageFile($stopwords);
                            $this->pattern = StopwordsPHP::create($this->language_file)->pattern();
                        }
                        $this->language = $stopwords;
                    } else if ($extension == 'pattern') {
                        $this->language = $stopwords;
                        $this->language_file = $stopwords;
                        $this->pattern = StopwordsPatternFile::create($this->language_file)->pattern();
                    } else if ($extension == 'php') {
                        $language_file = $stopwords;
                        $this->language = $stopwords;
                        $this->language_file = $language_file;
                        $this->pattern = StopwordsPHP::create($this->language_file)->pattern();
                    }
                }
            } elseif (is_subclass_of($stopwords, 'DonatelloZa\RakePlus\AbstractStopwordProvider')) {
                $this->pattern = $stopwords->pattern();
            } else {
                throw new InvalidArgumentException('Invalid stopwords list provided for RakePlus.');
            }

            if ($this->mb_support) {
                $sentences = $this->splitSentencesMb($text);
                $phrases = $this->getPhrasesMb($sentences, $this->pattern);
            } else {
                $sentences = $this->splitSentences($text);
                $phrases = $this->getPhrases($sentences, $this->pattern);
            }
            $word_scores = $this->calcWordScores($phrases);
            $this->phrase_scores = $this->calcPhraseScores($phrases, $word_scores);
        }

        return $this;
    }

    /**
     * Returns the extracted phrases.
     *
     * @return array
     */
    public function get()
    {
        return array_keys($this->phrase_scores);
    }

    /**
     * Returns the phrases and a score for each of
     * the phrases as an associative array.
     *
     * @return array
     */
    public function scores()
    {
        return $this->phrase_scores;
    }

    /**
     * Returns only the unique keywords within the
     * phrases instead of the full phrases itself.
     *
     * @return array
     */
    public function keywords()
    {
        $keywords = [];
        $phrases = $this->get();

        foreach ($phrases as $phrase) {
            $words = explode(' ', $phrase);
            foreach ($words as $word) {
                // This may look weird to the casual observer
                // but we do this since PHP will convert string
                // array keys that look like integers to actual
                // integers. This may cause problems further
                // down the line when a developer attempts to
                // append arrays to one another and one of them
                // have a mix of integer and string keys.
                if (!$this->filter_numerics || ($this->filter_numerics && !is_numeric($word))) {
                    if ($this->min_length === 0 || mb_strlen($word) >= $this->min_length) {
                        $keywords[$word] = $word;
                    }
                }
            }
        }

        return array_values($keywords);
    }

    /**
     * Sorts the phrases by score, use 'asc' or 'desc' to specify a
     * sort order.
     *
     * @param string $order Default is 'asc'
     *
     * @return $this
     */
    public function sortByScore($order = self::ORDER_ASC)
    {
        if ($order == self::ORDER_DESC) {
            arsort($this->phrase_scores);
        } else {
            asort($this->phrase_scores);
        }

        return $this;
    }

    /**
     * Sorts the phrases alphabetically, use 'asc' or 'desc' to specify a
     * sort order.
     *
     * @param string $order Default is 'asc'
     *
     * @return $this
     */
    public function sort($order = self::ORDER_ASC)
    {
        if ($order == self::ORDER_DESC) {
            krsort($this->phrase_scores);
        } else {
            ksort($this->phrase_scores);
        }

        return $this;
    }

    /**
     * Returns the current language being used.
     *
     * @return string
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * Returns the language file that was loaded. Will
     * be null if no file is loaded.
     *
     * @return string|null
     */
    public function languageFile()
    {
        return $this->language_file;
    }

    /**
     * Splits the text into an array of sentences.
     *
     * @param string $text
     *
     * @return array
     */
    private function splitSentences($text)
    {
        return preg_split('/' . $this->sentence_regex . '/',
            preg_replace('/' . $this->line_terminator . '/', ' ', $text));
    }

    /**
     * Splits the text into an array of sentences. Uses mb_* functions.
     *
     * @param string $text
     *
     * @return array
     */
    private function splitSentencesMb($text)
    {
        return mb_split($this->sentence_regex,
            mb_ereg_replace($this->line_terminator, ' ', $text));
    }

    /**
     * Split sentences into phrases by using the stopwords.
     *
     * @param array  $sentences
     * @param string $pattern
     *
     * @return array
     */
    private function getPhrases(array $sentences, $pattern)
    {
        $results = [];

        foreach ($sentences as $sentence) {
            $phrases_temp = preg_replace($pattern, '|', $sentence);
            $phrases = explode('|', $phrases_temp);
            foreach ($phrases as $phrase) {
                $phrase = mb_strtolower(trim($phrase));
                if (!empty($phrase)) {
                    if (!$this->filter_numerics || ($this->filter_numerics && !is_numeric($phrase))) {
                        if ($this->min_length === 0 || mb_strlen($phrase) >= $this->min_length) {
                            $results[] = $phrase;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Split sentences into phrases by using the stopwords. Makes use of
     * PHP's mb_* functions.
     *
     * @param array  $sentences
     * @param string $pattern
     *
     * @return array
     */
    private function getPhrasesMb(array $sentences, $pattern)
    {
        $results = [];

        foreach ($sentences as $sentence) {
            $phrases_temp = mb_eregi_replace($pattern, '|', $sentence);
            $phrases = explode('|', $phrases_temp);
            foreach ($phrases as $phrase) {
                $phrase = mb_strtolower(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $phrase));
                if (!empty($phrase)) {
                    if (!$this->filter_numerics || ($this->filter_numerics && !is_numeric($phrase))) {
                        if ($this->min_length === 0 || mb_strlen($phrase) >= $this->min_length) {
                            $results[] = $phrase;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Calculate a score for each word.
     *
     * @param array $phrases
     *
     * @return array
     */
    private function calcWordScores($phrases)
    {
        $frequencies = [];
        $degrees = [];

        foreach ($phrases as $phrase) {
            $words = $this->splitPhraseIntoWords($phrase);
            $words_count = count($words);
            $words_degree = $words_count - 1;

            foreach ($words as $w) {
                $frequencies[$w] = (isset($frequencies[$w])) ? $frequencies[$w] : 0;
                $frequencies[$w] += 1;
                $degrees[$w] = (isset($degrees[$w])) ? $degrees[$w] : 0;
                $degrees[$w] += $words_degree;
            }
        }

        foreach ($frequencies as $word => $freq) {
            $degrees[$word] += $freq;
        }

        $scores = [];
        foreach ($frequencies as $word => $freq) {
            $scores[$word] = (isset($scores[$word])) ? $scores[$word] : 0;
            $scores[$word] = $degrees[$word] / (float)$freq;
        }

        return $scores;
    }

    /**
     * Calculate score for each phrase by word scores.
     *
     * @param array $phrases
     * @param array $scores
     *
     * @return array
     */
    private function calcPhraseScores($phrases, $scores)
    {
        $keywords = [];

        foreach ($phrases as $phrase) {
            $keywords[$phrase] = (isset($keywords[$phrase])) ? $keywords[$phrase] : 0;
            $words = $this->splitPhraseIntoWords($phrase);
            $score = 0;

            foreach ($words as $word) {
                $score += $scores[$word];
            }

            $keywords[$phrase] = $score;
        }

        return $keywords;
    }

    /**
     * Split a phrase into multiple words and returns them
     * as an array.
     *
     * @param string $phrase
     *
     * @return array
     */
    private function splitPhraseIntoWords($phrase)
    {
        return array_filter(preg_split('/\W+/u', $phrase, -1, PREG_SPLIT_NO_EMPTY), function ($word) {
            return !is_numeric($word);
        });
    }

    /**
     * Returns the minimum number of letters each phrase/keyword must have.
     *
     * @return int
     */
    public function getMinLength()
    {
        return $this->min_length;
    }

    /**
     * Sets the minimum number of letters each phrase/keyword must have.
     *
     * @param int $min_length
     *
     * @return RakePlus
     */
    public function setMinLength($min_length)
    {
        if ((int)$min_length < 0) {
            throw new InvalidArgumentException('Minimum phrase length must be greater than or equal to 0.');
        }

        $this->min_length = (int)$min_length;
        return $this;
    }

    /**
     * Sets whether numeric-only phrases/keywords should be filtered
     * out or not.
     *
     * @param $filter_numerics
     *
     * @return RakePlus
     */
    public function setFilterNumerics($filter_numerics = true)
    {
        $this->filter_numerics = $filter_numerics;
        return $this;
    }

    /**
     * Returns whether numeric-only phrases/keywords will be filtered
     * out or not.
     *
     * @return bool
     */
    public function getFilterNumerics()
    {
        return $this->filter_numerics;
    }
}
