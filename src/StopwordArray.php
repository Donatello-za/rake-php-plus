<?php

namespace DonatelloZa\RakePlus;

class StopwordArray extends AbstractStopwordProvider
{
    /** @var array */
    protected $stopwords = [];

    /** @var string */
    protected $pattern = "";

    /**
     * StopwordArray constructor.
     *
     * @param array $stopwords
     */
    public function __construct(array $stopwords)
    {
        if (count($stopwords) > 0) {
            $this->stopwords = $stopwords;
            $this->pattern = $this->buildPatternFromArray($stopwords);
        } else {
            throw new \RuntimeException('The language array can not be empty.');
        }
    }

    /**
     * Constructs a new instance of the StopwordArray class.
     *
     * @param array $stopwords
     *
     * @return StopwordArray
     */
    public static function create(array $stopwords)
    {
        return (new self($stopwords));
    }

    /**
     * Returns a string containing a regular expression pattern.
     *
     * @return string
     */
    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * Returns an array of stopwords.
     *
     * @return array
     */
    public function stopwords()
    {
        return $this->stopwords;
    }
}