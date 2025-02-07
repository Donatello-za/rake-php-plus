<?php

namespace DonatelloZa\RakePlus;

interface ILangParseOptions
{
    /**
     * LangParseOptions constructor.
     *
     * @param string $language
     */
    public function __construct(string $language = 'en_US');

    /**
     * Instantiates a language parse options instance.
     *
     * @param string $language
     *
     * @return static
     */
    public static function create(string $language = 'en_US'): ILangParseOptions;

    /**
     * Set the text parsing options.
     *
     * @param string $sentence_regex The regular expression to use when
     *                               splitting sentences.
     *
     * @return static
     */
    public function setSentenceRegEx(string $sentence_regex): ILangParseOptions;

    /**
     * Returns the regular expression that is used to split sentences.
     *
     * @return string
     */
    public function getSentenceRegex(): string;

    /**
     * Returns the line terminator that is typically used in the source text.
     *
     * @return string
     */
    public function getLineTerminator(): string;

    /**
     * Sets the line terminator that is typically used in the source text.
     *
     * @param string $line_terminator
     *
     * @return ILangParseOptions
     */
    public function setLineTerminator(string $line_terminator): ILangParseOptions;
}
