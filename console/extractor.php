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
 * php -q extractor.php stopwords_en_US.txt
 *
 * To generate a regular expression pattern:
 * php -q extractor.php stopwords_en_US.txt -p
 *
 * To generate a regular expression pattern from a php array:
 * php -q extractor.php en_US.php -p
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
        echo "  php -q extractor.php stopwords_en_US.txt\n";
        echo "  php -q extractor.php stopwords_en_US.json\n";
        echo "\n";
        echo "For better RakePlus performance, use the -p switch to produce regular\n";
        echo "expression pattern instead of a PHP script.\n";
        echo "Example:\n";
        echo "  php -q extractor.php stopwords_en_US.txt -p\n";
        echo "  php -q extractor.php stopwords_en_US.json -p\n";
        echo "\n";
        echo "You can pipe the output of this tool directly into a\n";
        echo ".php or .pattern file:\n";
        echo "Example:\n";
        echo "  php -q extractor.php stopwords_en_US.txt > en_US.php\n";
        echo "  php -q extractor.php stopwords_en_US.json -p > un_US.pattern\n";
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
function get_arg($args, $arg_no, $default = null)
{
    if ($arg_no < count($args)) {
        return $args[$arg_no];
    } else {
        return $default;
    }
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

            return $stopwords;
        } else {
            echo "\n";
            echo "Error: Could not read text file \"{$stopwords_file}\".\n";
            echo "\n";
            exit(1);
        }
    }

    if ($ext === 'json') {
        $stopwords = json_decode(file_get_contents($stopwords_file), true);
        return array_fill_keys($stopwords, true);
    }

    if ($ext === 'php') {
        $stopwords = require $stopwords_file;
        return array_fill_keys($stopwords, true);
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

    $stopwords = array_keys($stopwords);
    asort($stopwords);

    echo "<?php\n";
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
    $stopwords = array_keys($stopwords);
    asort($stopwords);

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

    echo '/' . implode('|', $regex) . '/i' . "\n";
}

/**
 * @param array  $stopwords
 * @param string $stopwords_file
 * @param string $option
 *
 * @throws Exception
 */
function render_output(array $stopwords, $stopwords_file, $option)
{
    if (count($stopwords) > 0) {
        if ($option == '-p') {
            render_pattern_output($stopwords);
        } else {
            render_php_output($stopwords);
        }

    } else {
        echo "\n";
        echo "Error: No stopwords found in file \"{$stopwords_file}\".\n";
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
    $stopwords_file,
    get_arg($argv, 2)
);

