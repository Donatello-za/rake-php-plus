<?php

namespace DonatelloZa\RakePlus;

use InvalidArgumentException;

class RakePlus
{
    protected string $language = 'en_US';

    protected string $language_file = '';

    private ?string $pattern = null;

    private array $phrase_scores = [];

    private int $min_length = 0;

    private bool $filter_numerics = true;

    private string $sentence_regex;

    private string $line_terminator;

    public bool $mb_support = false;

    public ILangParseOptions $parseOptions;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * RakePlus constructor. Instantiates RakePlus and extracts
     * the key phrases from the text if supplied.
     *
     * If $stopwords is a string the method will:
     *
     * 1) Determine if it has a .pattern or .php extension and if
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
     * @param null|ILangParseOptions                $parseOptions      Additional text parsing options, see:
     *                                                                 @LangParseOptions
     */
    public function __construct(?string $text = null, $stopwords = 'en_US', int $phrase_min_length = 0,
                                bool $filter_numerics = true, ?ILangParseOptions $parseOptions = null)
    {
        $this->mb_support = extension_loaded('mbstring');

        $this->setMinLength($phrase_min_length);
        $this->setFilterNumerics($filter_numerics);

        if ($parseOptions === null) {
            $this->parseOptions = LangParseOptions::create(is_string($stopwords) ? $stopwords : $this->language);
        } else {
            $this->parseOptions = $parseOptions;
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
     * 1) Determine if it has a .pattern or .php extension and if
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
     * @param ILangParseOptions|null                $parseOptions      Additional text parsing options, see:
     *                                                                 @LangParseOptions
     *
     * @return RakePlus
     */
    public static function create(?string $text, $stopwords = 'en_US', int $phrase_min_length = 0,
                                  bool    $filter_numerics = true, ?ILangParseOptions $parseOptions = null): RakePlus
    {
        return (new self($text, $stopwords, $phrase_min_length, $filter_numerics, $parseOptions));
    }

    /**
     * Extracts the key phrases from the text.
     *
     * If $stopwords is a string the method will:
     *
     * 1) Determine if it has a .pattern or .php extension and if
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
    public function extract(string $text, $stopwords = 'en_US'): RakePlus
    {
        if (trim($text) === '') {
            return $this;
        }

        $this->initPattern($stopwords);

        $sentences = $this->splitSentences($text);
        $phrases = $this->getPhrases($sentences, $this->pattern);

        $word_scores = $this->calculateWordScores($phrases);
        $this->phrase_scores = $this->calculatePhraseScores($phrases, $word_scores);
        return $this;
    }

    /**
     * @param array|string|AbstractStopwordProvider $stopwords
     * @return void
     */
    protected function initPattern($stopwords): void
    {
        if (is_array($stopwords)) {
            $this->initPatternFromArray($stopwords);
            return;
        }

        if (is_string($stopwords)) {
            $this->initPatternFromString($stopwords);
            return;
        }

        if (is_object($stopwords) && is_a($stopwords, AbstractStopwordProvider::class)) {
            $this->initPatternFromProvider($stopwords);
            return;
        }

        throw new InvalidArgumentException('Invalid stopwords list provided for RakePlus.');
    }

    protected function initPatternFromArray($stopwords): void
    {
        $this->pattern = StopwordArray::create($stopwords)->pattern();
    }

    protected function initPatternFromString($stopwords): void
    {
        // @note ideally, this conditional should be called somehow
        if (!is_null($this->pattern) && ($this->language == $stopwords)) {
            return;
        }

        $extension = mb_strtolower(pathinfo($stopwords, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'pattern':
                $this->language = $stopwords;
                $this->language_file = $stopwords;
                $this->pattern = StopwordsPatternFile::create($this->language_file)->pattern();
                break;

            case 'php':
                $this->language = $stopwords;
                $this->language_file = $stopwords;
                $this->pattern = StopwordsPHP::create($this->language_file)->pattern();
                break;

            default:
                // First try the .pattern file
                $this->language_file = StopwordsPatternFile::languageFile($stopwords);
                if (file_exists($this->language_file)) {
                    $this->pattern = StopwordsPatternFile::create($this->language_file)->pattern();
                } else {
                    $this->language_file = StopwordsPHP::languageFile($stopwords);
                    $this->pattern = StopwordsPHP::create($this->language_file)->pattern();
                }
                $this->language = $stopwords;
        }
    }

    protected function initPatternFromProvider($stopwords): void
    {
        $this->pattern = $stopwords->pattern();
    }

    /**
     * Returns the extracted phrases.
     *
     * @return array
     */
    public function get(): array
    {
        return array_keys($this->phrase_scores);
    }

    /**
     * Returns the phrases and a score for each of
     * the phrases as an associative array.
     *
     * @return array
     */
    public function scores(): array
    {
        return $this->phrase_scores;
    }

    /**
     * Returns only the unique keywords within the
     * phrases instead of the full phrases itself.
     *
     * @return array
     */
    public function keywords(): array
    {
        $keywords = [];
        $phrases = $this->get();

        foreach ($phrases as $phrase) {
            $words = explode(' ', $phrase);
            foreach ($words as $word) {
                // This may look weird to the casual observer,
                // but we do this since PHP will convert string
                // array keys that look like integers to actual
                // integers. This may cause problems further
                // down the line when a developer attempts to
                // append arrays to one another and one of them
                // have a mix of integer and string keys.
                if (!$this->filter_numerics || !is_numeric($word)) {
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
     * @return RakePlus
     */
    public function sortByScore(string $order = self::ORDER_ASC): RakePlus
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
    public function sort(string $order = self::ORDER_ASC): RakePlus
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
    public function language(): string
    {
        return $this->language;
    }

    /**
     * Returns the language file that was loaded. Will
     * be null if no file is loaded.
     *
     * @return string|null
     */
    public function languageFile(): ?string
    {
        return $this->language_file;
    }

    /**
     * Splits the text into an array of sentences. Uses mb_* functions if available.
     *
     * @param string $text
     *
     * @return array
     */
    private function splitSentences(string $text): array
    {
        if ($this->mb_support) {
            return mb_split(
                $this->sentence_regex,
                mb_ereg_replace($this->line_terminator, ' ', $text),
            );
        }

        return preg_split(
            '/' . $this->sentence_regex . '/',
            preg_replace('/' . $this->line_terminator . '/', ' ', $text),
        );
    }

    /**
     * Split sentences into phrases by using the stopwords. Uses mb_* functions if available.
     *
     * @param array  $sentences
     * @param string $pattern
     *
     * @return array
     */
    private function getPhrases(array $sentences, string $pattern): array
    {
        $results = [];

        if ($this->mb_support) {
            foreach ($sentences as $sentence) {
                $phrases_temp = mb_eregi_replace($pattern, '|', $sentence);
                $phrases = explode('|', $phrases_temp);
                foreach ($phrases as $phrase) {
                    $phrase = mb_strtolower(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $phrase));
                    if (!empty($phrase)) {
                        if (!$this->filter_numerics || !is_numeric($phrase)) {
                            if ($this->min_length === 0 || mb_strlen($phrase) >= $this->min_length) {
                                $results[] = $phrase;
                            }
                        }
                    }
                }
            }

            return $results;
        }

        foreach ($sentences as $sentence) {
            $phrases_temp = preg_replace($pattern, '|', $sentence);
            $phrases = explode('|', $phrases_temp);
            foreach ($phrases as $phrase) {
                $phrase = strtolower(trim($phrase));
                if (!empty($phrase)) {
                    if (!$this->filter_numerics || !is_numeric($phrase)) {
                        if ($this->min_length === 0 || strlen($phrase) >= $this->min_length) {
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
    private function calculateWordScores(array $phrases): array
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
    private function calculatePhraseScores(array $phrases, array $scores): array
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
    private function splitPhraseIntoWords(string $phrase): array
    {
        $splitPhrase = preg_split('/\W+/u', $phrase, -1, PREG_SPLIT_NO_EMPTY);

        return array_filter($splitPhrase, static function ($word) {
            return !is_numeric($word);
        });
    }

    /**
     * Returns the minimum number of letters each phrase/keyword must have.
     *
     * @return int
     */
    public function getMinLength(): int
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
    public function setMinLength(int $min_length): RakePlus
    {
        if ($min_length < 0) {
            throw new InvalidArgumentException('Minimum phrase length must be greater than or equal to 0.');
        }

        $this->min_length = $min_length;
        return $this;
    }

    /**
     * Sets whether numeric-only phrases/keywords should be filtered
     * out or not.
     *
     * @param bool $filter_numerics
     *
     * @return RakePlus
     */
    public function setFilterNumerics(bool $filter_numerics = true): RakePlus
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
    public function getFilterNumerics(): bool
    {
        return $this->filter_numerics;
    }
}
