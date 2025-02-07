<?php

namespace DonatelloZa\RakePlus;

use RuntimeException;

class StopwordArray extends AbstractStopwordProvider
{
    protected array $stopwords = [];

    protected string $pattern = "";

    /**
     * @param array $stopwords
     */
    public function __construct(array $stopwords)
    {
        if (count($stopwords) > 0) {
            $this->stopwords = $stopwords;
            $this->pattern = $this->buildPatternFromArray($stopwords);
        } else {
            throw new RuntimeException('The language array can not be empty.');
        }
    }

    /**
     * Creates a new instance of the StopwordArray class.
     *
     * @param array $stopwords
     *
     * @return StopwordArray
     */
    public static function create(array $stopwords): StopwordArray
    {
        return (new self($stopwords));
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
}
