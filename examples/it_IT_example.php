<?php

// Per eseguire questo script:
// $ cd examples
// $ php it_IT_example.php "Un testo di esempio da cui estrarre le parole chiave"

require '../vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Inserisci il testo che vuoi che sia analizzato, per esempio:\n";
    echo "php it_IT_example.php \"Un testo di esempio da cui estrarre le parole chiave\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1], 'it_IT')->keywords();
print "Parole chiave estratte da \"{$argv[1]}\":\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1])->get();
print "Frasi estratte da \"{$argv[1]}\":\n";
print_r($phrases);
