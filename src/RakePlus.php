<?php

namespace DonatelloZa\RakePlus;

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
     * @param string|null                           $text
     * @param AbstractStopwordProvider|string|array $stopwords
     */
    public function __construct($text = null, $stopwords = 'en_US')
    {
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
     * @param string|null                           $text
     * @param AbstractStopwordProvider|string|array $stopwords
     *
     * @return RakePlus
     */
    public static function create($text, $stopwords = 'en_US')
    {
        return (new self($text, $stopwords));
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
        if (!empty(trim($text))) {
            if (is_array($stopwords)) {
                $this->pattern = StopwordArray::create($stopwords)->pattern();
            } else if (is_string($stopwords)) {
                if (is_null($this->pattern) || ($this->language != $stopwords)) {
                    $extension = strtolower(pathinfo($stopwords, PATHINFO_EXTENSION));
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
            } else if ($stopwords instanceof AbstractStopwordProvider) {
                $this->pattern = $stopwords->pattern();
            } else {
                throw new \InvalidArgumentException('Invalid stopwords list provided for RakePlus.');
            }

            $sentences = $this->splitSentences($text);
            $phrases = $this->getPhrases($sentences, $this->pattern);
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
     * Sorts the phrases by score, use 'asc' or 'desc' to specify a
     * sort order.
     *
     * @param string $order Default is 'asc'
     *
     * @return $this
     */
    public function sortByScore($order = 'asc')
    {
        if (strtolower(trim($order)) == 'desc') {
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
    public function sort($order = 'asc')
    {
        if (strtolower(trim($order)) == 'desc') {
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
        return preg_split('/[.?!,;\-"\'\(\)\\\x{2018}\x{2019}\x{2013}\t]+/u', $text);
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
                $phrase = strtolower(trim($phrase));
                if ($phrase != '') {
                    $results[] = $phrase;
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
            $words = $this->splitPhrase($phrase);
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
            $words = $this->splitPhrase($phrase);
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
     * @param string
     *
     * @return array
     */
    private function splitPhrase($phrase)
    {
        $words_temp = str_word_count($phrase, 1, '0123456789');
        $words = [];

        foreach ($words_temp as $word) {
            if ($word != '' and !(is_numeric($word))) {
                array_push($words, $word);
            }
        }

        return $words;
    }
}
