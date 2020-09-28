<?php

// Para ejecutar este ejemplo desde la línea de comando.:
// $ cd examples
// $ php es_AR_example.php "Algún texto de ejemplo del que me gustaría extraer palabras clave."

require '../vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Especifique el texto que desea analizar, por ejemplo:\n";
    echo "php es_AR_example.php \"Algún texto de ejemplo del que me gustaría extraer palabras clave.\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1], 'pt_BR')->keywords();
print "Resultados de palabras clave: \"{$argv[1]}\"\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1], 'pt_BR')->scores();
print "Resultados de la frase: \"{$argv[1]}\"\n";
print_r($phrases);
