<?php

namespace DonatelloZa\RakePlus;

class RakePlus
{
    /** @var string */
    protected $language = 'en_US';

    /** @var string */
    protected $language_file = "";

    /** @var array */
    protected $stopwords = [];

    /** @var string */
    private $stopword_pattern = [];

    /** @var array */
    private $phrase_scores = [];

    /**
     * RakePlus constructor. Instantiates RakePlus and extracts
     * the key phrases from the text if supplied. If the language
     * file is not found a RuntimeException will be thrown.
     *
     * If $language is a string the library will attempt to load
     * the stopwords from the lang/xxxx.php file. If $language is
     * a string and contains directory separators, it is used
     * directly as a filename. Finally,  you can also pass a flat
     * array containing the stopwords directly.
     *
     * @param string|null  $text
     * @param string|array $language (Default is en_US)
     */
    public function __construct($text = null, $language = 'en_US')
    {
        if (!is_null($text)) {
            $this->extract($text, $language);
        }
    }

    /**
     * Instantiates a RakePlus instance and extracts
     * the key phrases from the text. If the language file is not
     * found a RuntimeException will be thrown.
     *
     * If $language is a string the library will attempt to load
     * the stopwords from the lang/xxxx.php file. If $language is
     * a string and contains directory separators, it is used
     * directly as a filename. Finally,  you can also pass a flat
     * array containing the stopwords directly.
     *
     * @param string       $text
     * @param string|array $language (Default is en_US)
     *
     * @return RakePlus
     */
    public static function create($text, $language = 'en_US')
    {
        return (new self($text, $language));
    }

    /**
     * Extracts the key phrases from the text. If the language file is not
     * found a RuntimeException will be thrown.
     *
     * If $language is a string the library will attempt to load
     * the stopwords from the lang/xxxx.php file. If $language is
     * a string and contains directory separators, it is used
     * directly as a filename. Finally,  you can also pass a flat
     * array containing the stopwords directly.
     *
     * @param string       $text
     * @param string|array $language (Default is en_US)
     *
     * @return RakePlus
     */
    public function extract($text, $language = 'en_US')
    {
        if (!empty(trim($text))) {
            if (is_array($language)) {
                if (count($language) > 0) {
                    $this->stopwords = $language;
                    $this->stopword_pattern = $this->buildStopwordRegex($this->stopwords);
                } else {
                    throw new \RuntimeException('The language array can not be empty.');
                }
            } else {
                if (empty($this->language_file) || ($this->language != $language)) {
                    if (strpos($language, DIRECTORY_SEPARATOR) !== false) {
                        $language_file = $language;
                    } else {
                        $language_file = __DIR__ . '/../lang/' . $language . '.php';
                    }

                    $this->stopwords = $this->loadLanguageFile($language_file);

                    $this->language = $language;
                    $this->language_file = $language_file;
                    $this->stopword_pattern = $this->buildStopwordRegex($this->stopwords);
                }
            }

            $sentences = $this->splitSentences($text);
            $phrases = $this->getPhrases($sentences, $this->stopword_pattern);
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
     * Returns an array containing all the RAKE stopwords
     * that is currently loaded.
     *
     * @return array
     */
    public function stopwords()
    {
        return $this->stopwords;
    }

    /**
     * Loads the specified language file and returns with the results.
     *
     * @param string $language_file
     *
     * @return array
     */
    protected function loadLanguageFile($language_file)
    {
        if (!file_exists($language_file)) {
            throw new \RuntimeException('Could not find the RAKE stopwords file: ' . $language_file);
        } else {
            /** @noinspection PhpIncludeInspection */
            $stopwords = include($language_file);

            if (is_array($stopwords)) {
                if (count($stopwords) < 1) {
                    throw new \RuntimeException('No words found in RAKE stopwords file: ' . $language_file);
                } else {
                    return $stopwords;
                }
            } else {
                throw new \RuntimeException('Invalid results retrieved from RAKE stopwords file: ' . $language_file);
            }
        }
    }

    /**
     * Builds a string containing a big regular expression with all the
     * stopwords in it.
     *
     * @param array $stopwords
     *
     * @return string
     */
    private function buildStopwordRegex(array $stopwords)
    {
        $regex_array = [];

        foreach ($stopwords as $word) {
            $regex_array[] = '\b' . $word . '\b';
        }

        return '/' . implode('|', $regex_array) . '/i';
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
