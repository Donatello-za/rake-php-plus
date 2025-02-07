<?php

namespace DonatelloZa\RakePlus;

use RuntimeException;

class StopwordsPHP extends AbstractStopwordProvider
{
    protected array $stopwords = [];

    protected string $pattern = "";

    protected string $filename = "";

    /**
     * StopwordsPHP constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->stopwords = $this->loadLangPHPFile($filename);
        $this->pattern = $this->buildPatternFromArray($this->stopwords);
    }

    /**
     * Creates a new instance of the StopwordsPHP class.
     *
     * @param string $filename
     *
     * @return StopwordsPHP
     */
    public static function create(string $filename): StopwordsPHP
    {
        return (new self($filename));
    }

    /**
     * Creates a new instance of the StopwordsPHP class
     * but automatically determines the filename to use
     * based on the language string provided.
     *
     * The function looks in the ./lang directory for a file called
     * xxxx.php file where xxxx is the language string you specified.
     *
     * @param string $language (Default is en_US)
     *
     * @return StopwordsPHP
     */
    public static function createFromLanguage(string $language = 'en_US'): StopwordsPHP
    {
        return (new self(self::languageFile($language)));
    }

    /**
     * Returns the full path to the language file containing the
     * stopwords.
     *
     * @param string $language
     *
     * @return string
     */
    public static function languageFile(string $language = 'en_US'): string
    {
        return __DIR__ . '/../lang/' . $language . '.php';
    }

    /**
     * Returns a string containing a regular expression pattern.
     *
     * @return string
     */
    public function pattern(): string
    {
        return $this->pattern;
    }

    /**
     * Returns an array of stopwords.
     *
     * @return array
     */
    public function stopwords(): array
    {
        return $this->stopwords;
    }

    /**
     * Returns the originally supplied filename
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * Loads the specified language file and returns with the results.
     *
     * @param string $language_file
     *
     * @return array
     */
    protected function loadLangPHPFile(string $language_file): array
    {
        if (!file_exists($language_file)) {
            throw new RuntimeException("Could not find the RAKE stopwords file: $language_file");
        } else {
            $stopwords = include($language_file);

            if (is_array($stopwords)) {
                if (count($stopwords) < 1) {
                    throw new RuntimeException("No words found in RAKE stopwords file: $language_file");
                } else {
                    return $stopwords;
                }
            } else {
                throw new RuntimeException("Invalid results retrieved from RAKE stopwords file: $language_file");
            }
        }
    }
}
