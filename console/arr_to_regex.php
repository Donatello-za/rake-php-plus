<?php

/**
 * Read an array from a language stopword file, e.g. ./lang/en_US.php
 * and outputs it to a regular expression pattern.
 *
 * Usage:
 *
 * php ./arr_to_regex.php '../lang/en_US.php'
 *
 */

/**
 * Checks the number of supplied arguments.
 *
 * @param int $arg_count
 */
function check_args(int $arg_count)
{
    if ($arg_count < 2) {
        echo "\n";
        echo "Error: Please specify the .php filename containing the stopwords array.\n";
        echo "Example:\n";
        echo "  php ./console/arr_to_regex.php './lang/en_US.php'\n";
        echo "\n";
        exit(1);
    }
}

/**
 * Return the value of a specific argument from the array of arguments.
 *
 * @param array $args
 * @param int   $arg_no
 * @param mixed $default
 *
 * @return mixed
 */
function get_arg(array $args, int $arg_no, $default = null)
{
    if ($arg_no <= count($args)) {
        return $args[$arg_no];
    } else {
        return $default;
    }
}

/**
 * Loads from a .php file.
 *
 * @param string $php_file
 *
 * @return array
 */
function load_stopwords(string $php_file): array
{
    try {
        return require($php_file);
    } catch (Exception $e) {
        echo "\n";
        echo "Error: Could not read file \"$php_file\".\n";
        echo "\n";
        exit(1);
    }
}

/**
 * Render a reg-ex formatted output to console.
 *
 * @param array $stopwords
 */
function render_pattern_output(array $stopwords)
{
    asort($stopwords);

    $regex = [];

    foreach ($stopwords as $word) {
        $regex[] = '\b' . $word . '\b';
    }

    echo '/' . implode('|', $regex) . '/iu' . "\n";
}

/**
 * Render the output to console.
 *
 * @param array $stopwords
 * @param string $php_file
 */
function render_output(array $stopwords, string $php_file)
{
    if (count($stopwords) > 0) {
        render_pattern_output($stopwords);
    } else {
        echo "\n";
        echo "Error: No stopwords found in file \"$php_file\".\n";
        echo "\n";
        exit(1);
    }
}

check_args($argc);

$stopwords_file = get_arg($argv, 1);
$stopwords = load_stopwords($stopwords_file);

/** @noinspection PhpUnhandledExceptionInspection */
render_output(
    $stopwords,
    $stopwords_file
);
