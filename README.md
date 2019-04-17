# rake-php-plus
Yet another PHP implementation of the Rapid Automatic Keyword Extraction algorithm (RAKE).

[![Latest Stable Version](https://poser.pugx.org/donatello-za/rake-php-plus/v/stable)](https://packagist.org/packages/donatello-za/rake-php-plus)
[![Total Downloads](https://poser.pugx.org/donatello-za/rake-php-plus/downloads)](https://packagist.org/packages/donatello-za/rake-php-plus)
[![License](https://poser.pugx.org/donatello-za/rake-php-plus/license)](https://packagist.org/packages/donatello-za/rake-php-plus)

## Why is this package useful?

Keywords describe the main topics expressed in a document/text. Keyword *extraction* in turn allows for the extraction 
of important words and phrases from text. This in turn can be used for building a list of tags or to build a keyword 
search index or grouping similar content by its topics and much more. This library provides an easy method for PHP 
developers to get a list of keywords and phrases from a string of text.

This project is based on another project called [RAKE-PHP](https://github.com/Richdark/RAKE-PHP) by Richard Filipčík, 
which is a translation from a Python implementation simply called [RAKE](https://github.com/aneesha/RAKE).

*As described in: Rose, S., Engel, D., Cramer, N., & Cowley, W. (2010). 
[Automatic Keyword Extraction from Individual Documents](https://www.researchgate.net/publication/227988510_Automatic_Keyword_Extraction_from_Individual_Documents). 
In M. W. Berry & J. Kogan (Eds.), Text Mining: Theory and Applications: John Wiley & Sons.*


This particular package intends to include the following benefits over the original 
[RAKE-PHP](https://github.com/Richdark/RAKE-PHP) package:

1. Add [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
2. Implement [PSR-4](http://www.php-fig.org/psr/psr-4/) in order to be [Composer](https://getcomposer.org) installable.
3. Add additional functionality such as method chaining.
4. Add multiple ways to provide source stopwords.
5. Full unit test coverage.
6. Performance improvements.
7. Improved documentation.

## Currently Supported Languages

* English US (en_US)
* Spanish/español (es_AR)
* French/le français (fr_FR)
* Polish/język polski (pl_PL)
* Russian/русский язык (ru_RU)

## Version

v1.0.10

## Special Thanks

Special thanks goes out to [Jarosław Wasilewski](https://github.com/Orajo) for his contribution in adding the Polish
language and improving multi-byte support.

## Installation

### With Composer

`$ composer require donatello-za/rake-php-plus`

```json
{
    "require": {
        "donatello-za/rake-php-plus": "^1.0"
    }
}
```

```php
<?php
require 'vendor/autoload.php';

use DonatelloZa\RakePlus\RakePlus;
```

### Without Composer

```php
<?php

require 'path/to/AbstractStopwordProvider.php';
require 'path/to/StopwordArray.php';
require 'path/to/StopwordsPatternFile.php';
require 'path/to/StopwordsPHP.php';
require 'path/to/RakePlus.php';

use DonatelloZa\RakePlus\RakePlus;

```

## Example 1

Creates a new instance of RakePlus, extract the phrases and return the results. Assumes that the specified
text is English (US).


```php

use DonatelloZa\RakePlus\RakePlus;

$text = "Criteria of compatibility of a system of linear Diophantine equations, " . 
    "strict inequations, and nonstrict inequations are considered. Upper bounds " .
    "for components of a minimal set of solutions and algorithms of construction " .
    "of minimal generating sets of solutions for all types of systems are given.";

$phrases = RakePlus::create($text)->get();

print_r($phrases);

Array
(
    [0] => criteria
    [1] => compatibility
    [2] => system
    [3] => linear diophantine equations
    [4] => strict inequations
    [5] => nonstrict inequations
    [6] => considered
    [7] => upper bounds
    [8] => components
    [9] => minimal set
    [10] => solutions
    [11] => algorithms
    [12] => construction
    [13] => minimal generating sets
    [14] => types
    [15] => systems
)


```

## Example 2

Creates a new instance of RakePlus, extract the phrases in different different orders
and also shows how to get the phrase scores.

```php

use DonatelloZa\RakePlus\RakePlus;

$text = "Criteria of compatibility of a system of linear Diophantine equations, " . 
    "strict inequations, and nonstrict inequations are considered. Upper bounds " .
    "for components of a minimal set of solutions and algorithms of construction " .
    "of minimal generating sets of solutions for all types of systems are given.";

// Note: en_US is the default language.
$rake = RakePlus::create($text, 'en_US');

// 'asc' is optional and is the default sort order
$phrases = $rake->sort('asc')->get();
print_r($phrases);

Array
(
    [0] => algorithms
    [1] => compatibility
    [2] => components
    [3] => considered
    [4] => construction
    [5] => criteria
    [6] => linear diophantine equations
    [7] => minimal generating sets
    [8] => minimal set
    [9] => nonstrict inequations
    [10] => solutions
    [11] => strict inequations
    [12] => system
    [13] => systems
    [14] => types
    [15] => upper bounds
)

// Sort in descending order
$phrases = $rake->sort('desc')->get();
print_r($phrases);

Array
(
    [0] => upper bounds
    [1] => types
    [2] => systems
    [3] => system
    [4] => strict inequations
    [5] => solutions
    [6] => nonstrict inequations
    [7] => minimal set
    [8] => minimal generating sets
    [9] => linear diophantine equations
    [10] => criteria
    [11] => construction
    [12] => considered
    [13] => components
    [14] => compatibility
    [15] => algorithms
)

// Sort the phrases by score and return the scores
$phrase_scores = $rake->sortByScore('desc')->scores();
print_r($phrase_scores);

Array
(
    [linear diophantine equations] => 9
    [minimal generating sets] => 8.5
    [minimal set] => 4.5
    [strict inequations] => 4
    [nonstrict inequations] => 4
    [upper bounds] => 4
    [criteria] => 1
    [compatibility] => 1
    [system] => 1
    [considered] => 1
    [components] => 1
    [solutions] => 1
    [algorithms] => 1
    [construction] => 1
    [types] => 1
    [systems] => 1
)


// Extract phrases from a new string on the same RakePlus instance. Using the 
// same RakePlus instance is faster than creating a new instance as the 
// language files do not have to be re-loaded and parsed.

$text = "A fast Fourier transform (FFT) algorithm computes...";
$phrases = $rake->extract($text)->sort()->get();
print_r($phrases);

Array
(
    [0] => algorithm computes
    [1] => fast fourier transform
    [2] => fft
)

```

## Example 3

Creates a new instance of RakePlus and extract the unique keywords from the phrases.

```php

use DonatelloZa\RakePlus\RakePlus;

$text = "Criteria of compatibility of a system of linear Diophantine equations, " . 
    "strict inequations, and nonstrict inequations are considered. Upper bounds " .
    "for components of a minimal set of solutions and algorithms of construction " .
    "of minimal generating sets of solutions for all types of systems are given.";

$keywords = RakePlus::create($text)->keywords();
print_r($keywords);

Array
(
    [0] => criteria
    [1] => compatibility
    [2] => system
    [3] => linear
    [4] => diophantine
    [5] => equations
    [6] => strict
    [7] => inequations
    [8] => nonstrict
    [9] => considered
    [10] => upper
    [11] => bounds
    [12] => components
    [13] => minimal
    [14] => set
    [15] => solutions
    [16] => algorithms
    [17] => construction
    [18] => generating
    [19] => sets
    [20] => types
    [21] => systems
)
```

## Example 4

Creates a new instance of RakePlus without using the static RakePlus::create method.

```php

use DonatelloZa\RakePlus;

$text = "Criteria of compatibility of a system of linear Diophantine equations, " . 
    "strict inequations, and nonstrict inequations are considered. Upper bounds " .
    "for components of a minimal set of solutions and algorithms of construction " .
    "of minimal generating sets of solutions for all types of systems are given.";

$rake = new RakePlus();
$phrases = $rake->extract()->get();

// Alternative method:
$phrases = (new RakePlus($text))->get();

```

## Example 5

You can provide custom stopwords in four different ways:

```php

use DonatelloZa\RakePlus\RakePlus;

// 1: The standard way (provide a language code)
//    RakePlus will first look for ./lang/en_US.pattern, if
//    not found, it will look for ./lang/en_US.php.
$rake = RakePlus::create($text, 'en_US');

// 2: Pass an array containing stopwords
$rake = RakePlus::create($text, ['a', 'able', 'about', 'above', ...]);

// 3: Pass the name of a PHP or pattern file, 
//    see lang/en_US.php and lang/en_US.pattern for examples.
$rake = RakePlus::create($text, '/path/to/my/stopwords.pattern');

// 4: Create an instance of one of the stopword provider classes (or
//    create your own) and pass that to RakePlus:
$stopwords = StopwordArray::create(['a', 'able', 'about', 'above', ...]);
$rake = RakePlus::create($text, $stopwords);

```

## Example 6

You can specify the minimum number of characters that a phrase\keyword
must be and if less than the minimum it will be filtered out. The
default is 0 (no minimum).

```php

use DonatelloZa\RakePlus\RakePlus;

$text = '6462 Little Crest Suite, 413 Lake Carlietown, WA 12643';

// Without a minimum
$phrases = RakePlus::create($text, 'en_US', 0)->get();
print_r($phrases);

Array
(
    [0] => crest suite
    [1] => 413 lake carlietown
    [2] => wa 12643
)

// With a minimum
$phrases = RakePlus::create($text, 'en_US', 10)->get();
print_r($phrases);

Array
(
    [0] => crest suite
    [1] => 413 lake carlietown
)

```

## Example 7

You can specify whether phrases\keywords that consists of a numeric
number only should be filtered out or not. The default is to filter out
numerics.


```php

use DonatelloZa\RakePlus\RakePlus;

$text = '6462 Little Crest Suite, 413 Lake Carlietown, WA 12643';

// Filter out numerics
$phrases = RakePlus::create($text, 'en_US', 0, true)->get();
print_r($phrases);

Array
(
    [0] => crest suite
    [1] => 413 lake carlietown
    [2] => wa 12643
)

// Do not filter out numerics
$phrases = RakePlus::create($text, 'en_US', 0, false)->get();
print_r($phrases);

Array
(
    [0] => 6462
    [1] => crest suite
    [2] => 413 lake carlietown
    [3] => wa 12643
)

```

## How to add additional languages

**Using the stopwords extractor tool**

The library requires a list of "stopwords" for each language. Stopwords are 
common words used in a language such as "and", "are", "or", etc. An example 
list of such  stopwords can be found 
[here (en_US)](http://www.lextek.com/manuals/onix/stopwords2.html). You can
also [take a look at this list](https://github.com/Donatello-za/stopwords-json) 
which have stopwords for 50 different languages in individual JSON files.

When working with a simple list such as in the first example, you can copy and 
paste the text into a text file and use the extractor tool to
convert it into a format that this library can read efficiently. *An example
of such a stopwords file that have been copied from the hyperlink above have 
been included for your convenience (console/stopwords_en_US.txt)*

Alternatively you can extract the stopwords from a JSON file of which an
example have also been supplied, look under `console/stopwords_en_US.json`

To extract stopwords from a text file, run the following from the command line:

`$ php -q extractor.php stopwords_en_US.txt`

To extract stopwords from a JSON file, run the following from the command line:

`$ php -q extractor.php stopwords_en_US.json`

It will output the results to the terminal. You will notice that the results looks
like PHP and in fact it is. You can write the results directly to a PHP file by
piping it:

`$ php -q extractor.php stopwords_en_US.txt > en_US.php` 

Finally, copy the `en_US.php` file to the `lang/` directory (you may have to
set its permissions for the web server to execute it) and then instantiate
 php-rake-plus like so:

```php
$rake = RakePlus::create($text, 'en_US');
```
To improve the initial loading speed of the language file within RakePlus, you
can also set the exporter to produce the results as a regular expression pattern
using the `-p` switch:

`$ php -q extractor.php stopwords_en_US.txt -p > en_US.pattern` 

RakePHP will always look for a .pattern file first and if not found will look
for a .php file in the ./lang/ directory.

## To run tests

`./vendor/bin/phpunit tests/RakePlusTest.php`

## License

Released under MIT license (read LICENSE).
