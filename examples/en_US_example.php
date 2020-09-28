<?php

// To run this example from the command line:
// $ cd examples
// $ php en_US_example.php "Some example text from which I would like to extract keywords\"

require '../vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Please specify the text you would like to be parsed, e.g.:\n";
    echo "php en_US_example.php \"Some example text from which I would like to extract keywords\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1])->keywords();
print "The keywords for \"{$argv[1]}\" is:\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1])->get();
print "The phrases for \"{$argv[1]}\" is:\n";
print_r($phrases);

