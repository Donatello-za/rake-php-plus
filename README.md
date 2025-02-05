# rake-php-plus
A keyword and phrase extraction library based on the Rapid Automatic Keyword Extraction algorithm (RAKE).

[![Latest Stable Version](https://poser.pugx.org/donatello-za/rake-php-plus/v/stable)](https://packagist.org/packages/donatello-za/rake-php-plus)
[![Total Downloads](https://poser.pugx.org/donatello-za/rake-php-plus/downloads)](https://packagist.org/packages/donatello-za/rake-php-plus)
[![License](https://poser.pugx.org/donatello-za/rake-php-plus/license)](https://packagist.org/packages/donatello-za/rake-php-plus)

## Introduction

Keywords describe the main topics expressed in a document/text. Keyword *extraction* in turn allows for the extraction of important words and phrases from text. 

Extracted keywords can be used for things like:
- Building a list of useful tags out of a larger text
- Building search indexes and search engines
- Grouping similar content by its topic.

Extracted phrases can be used for things like:
- Highlighting important areas of a larger text
- Language or documentation analysis
- Building intelligent searches based on contextual terms

This library provides an easy method for PHP developers to get a list of keywords and phrases from a string of text 
and is based on another smaller and unmaintained project called [RAKE-PHP](https://github.com/Richdark/RAKE-PHP) by Richard Filipčík, 
which is a translation from a Python implementation simply called [RAKE](https://github.com/aneesha/RAKE).

> *As described in: Rose, S., Engel, D., Cramer, N., & Cowley, W. (2010).
[Automatic Keyword Extraction from Individual Documents](https://www.researchgate.net/publication/227988510_Automatic_Keyword_Extraction_from_Individual_Documents).
In M. W. Berry & J. Kogan (Eds.), Text Mining: Theory and Applications: John Wiley & Sons.*

This particular package intends to include the following benefits over the original [RAKE-PHP](https://github.com/Richdark/RAKE-PHP) package:

1. [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
2. [PSR-4](http://www.php-fig.org/psr/psr-4/) to be [Composer](https://getcomposer.org) installable.
3. Additional functionality such as method chaining.
4. Multiple ways to provide source stopwords.
5. Full unit test coverage.
6. Performance improvements.
7. Improved documentation.
8. Easy language integration and multibyte string support.

## Currently Supported Languages

* Afrikaans (af_ZA)
* Arabic (United Arab Emirates)/لإمارات العربية المتحدة (ar_AE)
* Brazilian Portuguese/português do Brasil (pt_BR)
* English US (en_US)
* European Portuguese/português europeu (pt_PT)
* French/le français (fr_FR)
* German (Germany)/Deutsch (Deutschland) (de_DE)
* Italian/italiano (it_IT)
* Polish/język polski (pl_PL)
* Russian/русский язык (ru_RU)
* Sorani Kurdish/سۆرانی (ckb_IQ)
* Spanish/español (es_AR)
* Tamil/தமிழ் (ta_TA)
* Turkish/Türkçe (tr_TR)
* Persian/Farsi/فارسی (fa_IR)
* Dutch/Nederlands (nl_NL)
* Swedish/svenska (sv_SE)

> If your language is not listed here it can be added, please see the section
called **[How to add additional languages](#how-to-add-additional-languages)** at the bottom of the page.

## Version

v1.0.20

## Special Thanks

* [Jarosław Wasilewski](https://github.com/Orajo): Polish language and improving multi-byte support.
* [Lev Morozov](https://github.com/levmorozov): French and Russian languages.
* [Igor Carvalho](https://github.com/Carvlho): Brazilian Portuguese language.
* [Khoshbin Ali Ahmed](https://github.com/Xoshbin): Sorani Kurdish and Arabic languages.
* [RhaPT](https://github.com/RhaPT): European Portuguese language.
* [Peter Thaleikis](https://github.com/spekulatius): German language.
* [Yusuf Usta](https://github.com/yusufusta): Turkish language.
* [orthosie](https://github.com/orthosie): Tamil language.
* [ScIEnzY](https://github.com/ScIEnzY): Italian language.
* [Reza Rabbani](https://github.com/thrashzone13): Persian language.
* [Anne van der Aar](https://github.com/annevanderaar): Dutch language.

## Installation

### With Composer

```bash
$ composer require donatello-za/rake-php-plus
```


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
require 'path/to/ILangParseOptions.php';
require 'path/to/LangParseOptions.php';
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
```

```
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

Creates a new instance of RakePlus, extract the phrases in different orders
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
```

```
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
```

```php
// Sort in descending order
$phrases = $rake->sort('desc')->get();
print_r($phrases);
```

```
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
```

```php
// Sort the phrases by score and return the scores
$phrase_scores = $rake->sortByScore('desc')->scores();
print_r($phrase_scores);
```

```
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
```

```php
// Extract phrases from a new string on the same RakePlus instance. Using the
// same RakePlus instance is faster than creating a new instance as the
// language files do not have to be re-loaded and parsed.

$text = "A fast Fourier transform (FFT) algorithm computes...";
$phrases = $rake->extract($text)->sort()->get();
print_r($phrases);
```

```
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
```

```
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
```

```
Array
(
    [0] => crest suite
    [1] => 413 lake carlietown
    [2] => wa 12643
)
```

```php
// With a minimum
$phrases = RakePlus::create($text, 'en_US', 10)->get();

print_r($phrases);
```

```
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
```

```Array
(
    [0] => crest suite
    [1] => 413 lake carlietown
    [2] => wa 12643
)
```

```php
// Do not filter out numerics
$phrases = RakePlus::create($text, 'en_US', 0, false)->get();

print_r($phrases);
```

```
Array
(
    [0] => 6462
    [1] => crest suite
    [2] => 413 lake carlietown
    [3] => wa 12643
)
```

## How to add additional languages

The library requires a list of "stopwords" for each language. Stopwords are common words used in a language such as "and", "are", "or", etc.

There are [stopwords for 50 languages](https://github.com/Donatello-za/stopwords-json#languages) (including the ones already supported) available in JSON format.
If you are lucky enough to have your language listed then you can easily import it into the library. To
do so, read the section below:

**Using the stopwords extractor tool**

> Note: These instructions assumes you are using Linux

We will be using the Greek language as an example:

1. Check to see if your operating have the Greek localisation files, the Greek locale
   code you have to look for is: `el_GR`. So run the command `$ locale -a` to see if it is listed.
2. If it is not listed, you'll need to create it, so run:

```sh
sudo locale-gen el_GR
sudo locale-gen el_GR.utf8
```

3. Go the [list of stopword files](https://github.com/Donatello-za/stopwords-json#languages)  and
find the Greek language, the file will be called `el.json` and it will contain 75 stopwords.
4. Download the `el.json` file and store it somewhere on your system.
5. In you terminal, go to the directory of the `rake-php-plus` library, it will 
   be under `vendor/donatello-za/rake-php-plus` if you used Composer to install it.

We now need to use the JSON file to create two new files, one will be a `.php` file
that contains the stopwords as a PHP array and one fill be a `.pattern` file which
is a text file containing the stopwords as a regular expression:

1. Extract and convert the .json file to a PHP file by running:

```sh
$ php ./console/extractor.php path/to/el.json --locale=el_GR --output=php > ./some/dir/el_GR.php
```

2. Extract and convert the .json file to a .pattern file by running:

```sh
$ php ./console/extractor.php path/to/el.json --locale=el_GR --output=pattern > ./some/dir/el_GR.pattern
```

That is it! You can now use the new stopwords by specifying it when creating an instance
of the RakePlus class, for example:

```php
$rake = RakePlus::create($text, '/some/dir/el_GR.pattern');
```

or

```php
$rake = RakePlus::create($text, '/some/dir/el_GR.php');
```

**Contribute by Adding a Language**

If you want your language to be officially support, you can fork this library,
generate the `.pattern` and `.php` stopword files as described above, place it
in the `./rake-php-plus/lang/` directory and submit it as a pull request.

Once your language is officially supported, you'll be able to specify the language
without having to specify the file to use, for example:

```php
$rake = RakePlus::create($text, 'el_GR');
```

RakePHP will always look for a `.pattern` file first and if not found it will 
look for a `.php` file in the `./lang/` directory.

**I don't have a stopwords file for my language, what now?**

If your language is not covered in the [list of 50 languages here](https://github.com/Donatello-za/stopwords-json#languages)
you may have to try and find it elsewhere, try searching for "yourlanguage stopwords". If you
find a list or decide to create your own list, you can also just place it in a standard text
file instead of a .json file and extract the stopwords using the extractor tool, for
example:

```sh
$ php ./console/extractor.php path/to/mystopwords.txt --locale=LOCAL_CODE --output=php > ./some/dir/LOCAL_CODE.php
$ php ./console/extractor.php path/to/mystopwords.txt --locale=LOCAL_CODE --output=php > ./some/dir/LOCAL_CODE.php
```

*Remember to replace `LOCAL_CODE` for the correct local you wish to use.*

Here is an example text file containing stopwords that was copied and pasted from a 
site: [stopwords_en_US](./console/stopwords_en_US.txt)

## To run tests

Unit testing is performed using PHPUnit v11.2 running on PHP v8.3.0+.

`./vendor/bin/phpunit tests`

## License

Released under MIT license (read LICENSE).
