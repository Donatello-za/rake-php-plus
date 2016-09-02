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
 * php -q extractor.php stopwords.txt
 *
 */

if ($argc < 2) {
    echo "\n";
    echo "Error: Please specify the filename of the stopwords file to extract.\n";
    echo "Example:\n";
    echo "  php -q extractor.php stopwords.txt\n";
    echo "\n";
    exit(1);
}

$stopwords_file = $argv[1];

$stopwords = [];

if ($h = @fopen($stopwords_file, 'r')) {
    while (($line = fgets($h)) !== false) {
        $line = trim($line);
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

$stopword_count = count($stopwords);
if (count($stopwords) > 0) {
    echo "\n";
    echo "Success: {$stopword_count} stopword(s) found in file \"{$stopwords_file}\".\n";
    echo "\n";

    $timestamp = (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::ATOM);

    $stopwords = array_keys($stopwords);
    asort($stopwords);

    echo "<?php\n";
    echo "\n";
    echo "/**\n";
    echo " * Stopwords list built by Gerard Salton and Chris Buckley.\n";
    echo " * Source: http://www.lextek.com/manuals/onix/stopwords2.html\n";
    echo " *\n";
    echo " * Extracted using extractor.php @ {$timestamp} \n";
    echo " */\n";
    echo "\n";
    echo '$rake_stopwords = [' . "\n";

    for ($i = 0; $i < $stopword_count; $i++) {
        if ($i == ($stopword_count - 1)) {
            echo "    '" . str_replace("'", "\\'", $stopwords[$i]) . "'\n";
        } else {
            echo "    '" . str_replace("'", "\\'", $stopwords[$i]) . "',\n";
        }
    }

    echo "];\n";
    echo "\n";
} else {
    echo "\n";
    echo "Error: No stopwords found in file \"{$stopwords_file}\".\n";
    echo "\n";
    exit(1);
}

