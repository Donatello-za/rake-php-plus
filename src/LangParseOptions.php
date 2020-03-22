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
    /** @var string */
    private $language;

    /** @var string */
    private $sentence_regex;

    /** @var string */
    private $line_terminator;

    /**
     * LangParseOptions constructor.
     *
     * @param string $language
     */
    public function __construct($language = 'en_US')
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
     * @return $this
     */
    public static function create($language = 'en_US')
    {
        return (new self($language));
    }

    /**
     * Returns the language that was specified when instantiating the options class.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the text parsing options.
     *
     * @param string $sentence_regex The regular expression to use when
     *                               splitting sentences.
     *
     * @return $this
     */
    public function setSentenceRegEx($sentence_regex)
    {
        $this->sentence_regex = $sentence_regex;
        return $this;
    }

    /**
     * Returns the regular expression that is used to split sentences.
     *
     * @return string
     */
    public function getSentenceRegex()
    {
        return $this->sentence_regex;
    }

    /**
     * Returns the line terminator that is typically used in the source text.
     *
     * @return string
     */
    public function getLineTerminator()
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
    public function setLineTerminator($line_terminator)
    {
        $this->line_terminator = $line_terminator;
        return $this;
    }
}
