<?php

/**
 * Extracts stopwords from a file copied and pasted from
 * http://www.lextek.com/manuals/onix/stopwords2.html
 *
 * and produces an output containing the contents for a
 * PHP language file containing an array with all the
 * stopwords.
 *
 * Usage:
 *
 * php -q extractor.php stopwords_en_US.txt
 *
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
        echo "\n";
        echo "For better RakePlus performance, use the -p switch to produce regular\n";
        echo "expression pattern instead of a PHP script.\n";
        echo "Example:\n";
        echo "  php -q extractor.php stopwords_en_US.txt -p\n";
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
    if ($arg_no <= count($args)) {
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

    if ($h = @fopen($stopwords_file, 'r')) {
        while (($line = fgets($h)) !== false) {
            $line = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $line);
            if (!empty($line) && $line[0] != '#') {
                $stopwords[$line] = true;
            }
        }
    } else {
        echo "\n";
        echo "Error: Could not read file \"{$stopwords_file}\".\n";
        echo "\n";
        exit(1);
    }

    return $stopwords;
}

/**
 * @param array  $stopwords
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
        $regex[] = '\b' . $word . '\b';
    }

    echo '/' . implode('|', $regex) . '/iu' . "\n";
}

/**
 * @param array  $stopwords
 * @param string $stopwords_file
 * @param string $option
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

render_output(
    $stopwords,
    $stopwords_file,
    get_arg($argv, 2)
);

