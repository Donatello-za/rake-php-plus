<?php

namespace DonatelloZa\RakePlus;

class RakePlus
{
    /** @var string */
    protected $language = 'en_US';

    /** @var string */
    protected $language_file = null;

    /** @var array */
    protected $stopwords = [];

    /**
     * RakePlus constructor. Instantiates RakePlus and extracts
     * the key phrases from the text if supplied. If the language
     * file is not found a RuntimeException will be thrown.
     *
     * @param string|null $text
     * @param string      $language (Default is en_US)
     */
    public function __construct($text = null, $language = 'en_US')
    {
        if (!is_null($text)) {
            $this->extractFrom($text, $language);
        }
    }

    /**
     * Instantiates a RakePlus instance and extracts
     * the key phrases from the text. If the language file is not
     * found a RuntimeException will be thrown.
     *
     * @param string $text
     * @param string $language (Default is en_US)
     *
     * @return RakePlus
     */
    public static function extract($text, $language = 'en_US')
    {
        return new self($text, $language);
    }

    /**
     * Extracts the key phrases from the text. If the language file is not
     * found a RuntimeException will be thrown.
     *
     * @param string $text
     * @param string $language (Default is en_US)
     *
     * @return RakePlus
     */
    public function extractFrom($text, $language = 'en_US')
    {
        $this->language_file = '../lang/' . $language . '.php';

        if (!file_exists($this->language_file)) {
            throw new \RuntimeException('Could not find the RAKE stopwords file: ' . $this->language_file);
        } else {
            /** @noinspection PhpIncludeInspection */
            $this->stopwords = include($this->language_file);

            if (is_array($this->stopwords)) {
                if (count($this->stopwords) < 1) {
                    throw new \RuntimeException('No words found in RAKE stopwords file: ' . $this->language_file);
                }
            } else {
                throw new \RuntimeException('Invalid results retrieved from RAKE stopwords file: ' . $this->language_file);
            }
        }
    }
}
