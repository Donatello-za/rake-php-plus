<?php

namespace DonatelloZa\RakePlus;

abstract class AbstractStopwordProvider
{
    /**
     * Returns a string containing a regular expression pattern.
     */
    abstract public function pattern();

    /**
     * Builds a string containing a big regular expression with all the
     * stopwords in it.
     *
     * @param array $stopwords
     *
     * @return string
     */
    protected function buildPatternFromArray(array $stopwords)
    {
        $pattern = [];

        foreach ($stopwords as $word) {
            if (mb_strlen($word) === 1) {
                // This pattern allows for words such as a-class and j'aimerais
                $pattern[] = '\b' . $word . '(?!(-|\'))\b';
            } else {
                $pattern[] = '\b' . $word . '\b';
            }
        }

        if (extension_loaded('mbstring')) {
            return implode('|', $pattern);
        } else {
            return '/' . implode('|', $pattern) . '/i';
        }
    }
}
