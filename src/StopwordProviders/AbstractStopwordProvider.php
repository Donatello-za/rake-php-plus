<?php

namespace DonatelloZa\RakePlus\StopwordProviders;

abstract class AbstractStopwordProvider
{
    /**
     * Returns a string that contains a regular expression pattern.
     *
     * @return string
     */
    abstract public function pattern(): string;

    /**
     * Builds a string that contains a big regular expression with all the
     * stopwords in it.
     *
     * @param array $stopwords
     *
     * @return string
     */
    protected function buildPatternFromArray(array $stopwords): string
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

        return implode('|', $pattern);
    }
}
