<?php

namespace DonatelloZa\RakePlus;

/**
 * Class LangParseOptions
 *
 * Provides some default parsing options for known
 * languages.
 */
class LangParseOptions implements ILangParseOptions
{
    private string $language;

    private string $sentence_regex;

    private string $line_terminator;

    /**
     * LangParseOptions constructor.
     *
     * @param string $language
     */
    public function __construct(string $language = 'en_US')
    {
        $this->language = $language;

        $this->setLineTerminator("\n");

        switch ($language) {
            case 'ckb_IQ':
            case 'ar_AE':
                $this->setSentenceRegEx('[-؛؟،“.!?,;:\t\"\(\)]');
                break;

            default:
                $this->setSentenceRegEx('[.!?,;:\t\"\(\)]');
        }
    }

    /**
     * Instantiates a language parse options instance.
     *
     * @param string $language
     *
     * @return static
     */
    public static function create(string $language = 'en_US'): ILangParseOptions
    {
        return (new self($language));
    }

    /**
     * Returns the language that was specified when instantiating the options class.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Set the text parsing options.
     *
     * @param string $sentence_regex The regular expression to use when
     *                               splitting sentences.
     *
     * @return static
     */
    public function setSentenceRegEx(string $sentence_regex): ILangParseOptions
    {
        $this->sentence_regex = $sentence_regex;
        return $this;
    }

    /**
     * Returns the regular expression that is used to split sentences.
     *
     * @return string
     */
    public function getSentenceRegex(): string
    {
        return $this->sentence_regex;
    }

    /**
     * Returns the line terminator that is typically used in the source text.
     *
     * @return string
     */
    public function getLineTerminator(): string
    {
        return $this->line_terminator;
    }

    /**
     * Sets the line terminator that is typically used in the source text.
     *
     * @param string $line_terminator
     *
     * @return $this
     */
    public function setLineTerminator(string $line_terminator): ILangParseOptions
    {
        $this->line_terminator = $line_terminator;
        return $this;
    }
}
