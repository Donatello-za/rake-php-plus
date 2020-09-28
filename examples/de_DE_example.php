<?php

// To run this example from the command line:
// $ cd examples
// $ php de_DE_example.php "Ein Beispieltext aus dem du die Schlüsselwörter extrahieren möchtest."

require '../vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Please specify the text you would like to be parsed, e.g.:\n";
    echo "php de_DE_example.php \"Ein Beispieltext aus dem du die Schlüsselwörter extrahieren möchtest.\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1], 'de_DE')->keywords();
print "The keywords for \"{$argv[1]}\" is:\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1], 'de_DE')->get();
print "The phrases for \"{$argv[1]}\" is:\n";
print_r($phrases);

