<?php

// برای اجرای این مثال از طریق کامند لاین :
// $ cd examples
// $ php fa_IR_example.php "یک جمله نمونه برای استخراج کلمات از آن\"

require '../vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;

if ($argc < 2) {
    echo "Please specify the text you would like to be parsed, e.g.:\n";
    echo "php fa_IR_example.php \"یک جمله نمونه برای استخراج کلمات از آن\"\n";
    exit(1);
}

$keywords = RakePlus::create($argv[1])->keywords();
print "The keywords for \"{$argv[1]}\" is:\n";
print_r($keywords);

$phrases = RakePlus::create($argv[1])->get();
print "The phrases for \"{$argv[1]}\" is:\n";
print_r($phrases);
