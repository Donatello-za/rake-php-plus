<?php

// Pour exécuter cet exemple à partir de la ligne de commande
// php ./examples/fr_FR_example.php "Un exemple de texte"

require 'vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Spécifiez le texte que vous souhaitez analyser, par exemple:\n";
    echo "php ./examples/fr_FR_example.php \"Quelques exemples de texte dont j'aimerais extraire des mots-clés.\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1], 'fr_FR')->keywords();
print "Résultats de mots clés: {$argv[1]}\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1], 'fr_FR')->get();
print "Résultats de la phrase: {$argv[1]}\n";
print_r($phrases);
