<?php

/**
 * Stopwords are either supplied in simple text files that
 * are copied from web pages such as this:
 * http://www.lextek.com/manuals/onix/stopwords2.html
 *
 * or it can be supplied as a .json file that is stored in the
 * format ["a","a's","able","about","above", .... ]
 *
 * This tool extracts the stopwords from these files and
 * produces either a .php output (containing a PHP array)
 * or a .pattern file containing a regular expression pattern.
 *
 * Usage:
 * To generate PHP output:
 * php -q extractor.php stopwords_en_US.txt --output=php
 *
 * To generate a regular expression pattern:
 * php -q extractor.php stopwords_en_US.txt --output=pattern
 *
 * To generate a regular expression pattern from a php array:
 * php -q extractor.php en_US.php --output=pattern
 *
 * Sorting the keywords in descending order, e.g. Z -> A is
 * important and for the tool to sort languages other than
 * English properly it needs to set the locale using PHP's
 * setlocale() function which depends on your system's
 * available locals. To check your locals on Linux run:
 *
 * $ local -a
 *
 * To install more locals:
 *
 * $ sudo locale-gen es_AR
 * $ sudo locale-gen es_AR.utf8
 */

/**
 * @param int $arg_count
 */
function check_args($arg_count)
{
    if ($arg_count < 2) {
        echo "\n";
        echo "Error: Please specify the filename of the stopwords file to extract.\n";
        echo "Example:\n";
        echo "  php ./console/extractor.php stopwords_en_US.txt --locale=en_US --output=php\n";
        echo "  php ./console extractor.php stopwords_en_US.json --locale=en_US --output=php\n";
        echo "\n";
        echo "For better RakePlus performance, use the --output argument to produce\n";
        echo "regular expression pattern instead of a PHP script.\n";
        echo "Example:\n";
        echo "  php ./console/extractor.php stopwords_en_US.txt --locale=en_US --output=pattern\n";
        echo "  php ./console/extractor.php stopwords_en_US.json --locale=en_US --output=pattern\n";
        echo "\n";
        echo "You can pipe the output of this tool directly into a\n";
        echo ".php or .pattern file:\n";
        echo "Example:\n";
        echo "  php ./console/extractor.php stopwords_en_US.txt --locale=en_US --output=php > en_US.php\n";
        echo "  php ./console/extractor.php stopwords_en_US.json --locale=en_US --output=pattern > en_US.pattern\n";
        echo "  php ./console/extractor.php en_US.php --locale=en_US --output=pattern > en_US.pattern\n";
        echo "\n";

        exit(1);
    }
}

/**
 * @param array $args
 * @param int   $arg_no
 * @param mixed $default
 *
 * @return mixed
 */
function get_arg_by_index($args, $arg_no, $default = null)
{
    if ($arg_no < count($args)) {
        return $args[$arg_no];
    } else {
        return $default;
    }
}

/**
 * @param array $args
 * @param string $name
 * @param mixed $default
 *
 * @return mixed
 */
function get_arg_by_name($args, $name, $default = null)
{
    foreach ($args as $arg) {
        list($key, $value) = array_pad(explode('=', $arg), 2, $default);
        if ($key == $name) {
            return $value;
        }
    }

    return $default;
}

/**
 * Returns true if one if the arguments consists
 * of the supplied $arg.
 *
 * @param $args
 * @param $name
 *
 * @return mixed
 */
function has_arg($args, $name)
{
    foreach ($args as $arg) {
        if ($arg == $name) {
            return true;
        }
    }

    return false;
}

/**
 * @param string $stopwords_file
 *
 * @return array
 */
function load_stopwords($stopwords_file)
{
    $stopwords = [];

    $ext = pathinfo($stopwords_file, PATHINFO_EXTENSION);
    if (!file_exists($stopwords_file)) {
        echo "\n";
        echo "Error: Stopwords file \"{$stopwords_file}\" not found.\n";
        echo "\n";
        exit(1);
    }

    if ($ext === 'txt') {
        if ($h = @fopen($stopwords_file, 'r')) {
            while (($line = fgets($h)) !== false) {
                $line = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $line);
                if (!empty($line) && $line[0] != '#') {
                    $stopwords[$line] = true;
                }
            }

            return array_keys($stopwords);
        } else {
            echo "\n";
            echo "Error: Could not read text file \"{$stopwords_file}\".\n";
            echo "\n";
            exit(1);
        }
    }

    if ($ext === 'json') {
        $stopwords = json_decode(file_get_contents($stopwords_file), true);
        return array_keys(array_fill_keys($stopwords, true));
    }

    if ($ext === 'php') {
        /** @noinspection PhpIncludeInspection */
        $stopwords = require $stopwords_file;
        return array_keys(array_fill_keys($stopwords, true));
    }

    return [];
}

/**
 * @param array $stopwords
 *
 * @throws Exception
 */
function render_php_output(array $stopwords)
{
    $stopword_count = count($stopwords);
    $timestamp = (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::ATOM);

    echo "\xEF\xBB\xBF<?php\n";
    echo "\n";
    echo "/**\n";
    echo " * Stopwords list for the use in the PHP package rake-php-plus.\n";
    echo " * See: https://github.com/Donatello-za/rake-php-plus\n";
    echo " *\n";
    echo " * Extracted using extractor.php @ {$timestamp} \n";
    echo " */\n";
    echo "\n";
    echo 'return [' . "\n";

    for ($i = 0; $i < $stopword_count; $i++) {
        if ($i == ($stopword_count - 1)) {
            echo "    '" . str_replace("'", "\\'", $stopwords[$i]) . "'\n";
        } else {
            echo "    '" . str_replace("'", "\\'", $stopwords[$i]) . "',\n";
        }
    }

    echo "];\n";
    echo "\n";
}

/**
 * @param array $stopwords
 */
function render_pattern_output(array $stopwords)
{
    $regex = [];

    foreach ($stopwords as $word) {
        if (mb_strlen($word) === 1) {
            // This pattern allows for words such as a-class and j'aimerais, however,
            // words such as day-z will be broken up into day- and the z will go
            // missing. A possible workaround is to set the pattern as:
            // '\b(?!-)' . $word . '(?!(-|\'))\b'
            // but then two character words such as WA will also be stripped out.
            $regex[] = '\b' . $word . '(?!(-|\'))\b';
            // $regex[] = '\b(?!-)' . $word . '(?!(-|\'))\b';
        } else {
            $regex[] = '\b' . $word . '\b';
        }
    }

    echo "\xEF\xBB\xBF".'/' . implode('|', $regex) . '/i' . "\n";
}

/**
 * @param array $stopwords
 */
function render_json_output(array $stopwords)
{
    echo json_encode($stopwords, JSON_PRETTY_PRINT) . "\n";
}

/**
 * @param array  $stopwords
 * @param string $stopwords_file
 * @param string $output
 *
 * @throws Exception
 */
function render_output(array $stopwords, $stopwords_file, $output = 'php')
{
    if (count($stopwords) > 0) {
        if ($output == 'pattern') {
            render_pattern_output($stopwords);
        } else if ($output == 'php') {
            render_php_output($stopwords);
        } else if ($output == 'json') {
            render_json_output($stopwords);
        }

    } else {
        echo "\n";
        echo "Error: No stopwords found in file \"{$stopwords_file}\".\n";
        echo "\n";
        exit(1);
    }
}

check_args($argc);

$stopwords_file = get_arg_by_index($argv, 1);
$stopwords = load_stopwords($stopwords_file);

$locale = get_arg_by_name($argv, '--locale');
if ($locale === null) {
    echo "Please specify the locale, e.g. --locale=en_US\n";
}

if (!has_arg($argv, '--nosort')) {
    $result = setlocale(LC_COLLATE, $locale . '.utf8');
    if (!has_arg($argv, '--ascending')) {
        usort($stopwords, function ($a, $b) {
            return strcoll($b, $a);
        });
    } else {
        usort($stopwords, function ($a, $b) {
            return strcoll($a, $b);
        });
    }

    /*
    if (!has_arg($argv, '--ascending')) {
        rsort($stopwords);
    } else {
        sort($stopwords);
    }
    */
}

$OUTPUT_TYPES = ['pattern', 'php', 'json'];
$output = get_arg_by_name($argv, '--output');
if (!in_array($output, $OUTPUT_TYPES)) {
    echo "Please specify the output format, e.g. --output=pattern, --output=php or --output=json\n";
    exit(1);
}

/** @noinspection PhpUnhandledExceptionInspection */
render_output($stopwords, $stopwords_file, $output);

