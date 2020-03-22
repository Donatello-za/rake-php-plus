<?php

namespace DonatelloZa\RakePlus;

interface ILangParseOptions
{
    /**
     * LangParseOptions constructor.
     *
     * @param string $language
     */
    public function __construct($language = 'en_US');

    /**
     * Instantiates a language parse options instance.
     *
     * @param string $language
     *
     * @return $this
     */
    public static function create($language = 'en_US');

    /**
     * Set the text parsing options.
     *
     * @param string $sentence_regex The regular expression to use when
     *                               splitting sentences.
     *
     * @return $this
     */
    public function setSentenceRegEx($sentence_regex);

    /**
     * Returns the regular expression that is used to split sentences.
     *
     * @return string
     */
    public function getSentenceRegex();

    /**
     * Returns the line terminator that is typically used in the source text.
     *
     * @return string
     */
    public function getLineTerminator();

    /**
     * Sets the line terminator that is typically used in the source text.
     *
     * @param string $line_terminator
     *
     * @return LangParseOptions
     */
    public function setLineTerminator($line_terminator);
}
