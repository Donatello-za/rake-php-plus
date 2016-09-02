# rake-php-plus
Yet another PHP implementation of the Rapid Automatic Keyword Extraction algorithm (RAKE).

This project is based on another project: [RAKE-PHP](https://github.com/Richdark/RAKE-PHP) by Richard Filipčík, which
in turn was based from a Python implementation: [RAKE](https://github.com/aneesha/RAKE)

As described in: ROSE, Stuart, et al. Automatic keyword extraction from individual documents. Text Mining, 2010, 1-20.

This particular package intends to include the following benefits over the original RAKE-PHP package:

1. Add [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
2. Implement [PSR-4](http://www.php-fig.org/psr/psr-4/) in order to be [Composer](https://getcomposer.org) installable.
3. Add additional functionality such as method chaining.
4. Add multiple ways to provide source stopwords.
5. Full unit test coverage.
6. Performance improvements.
7. Improved documentation.
8. Easy Laravel installation/integration.

## Version

0.1 Alpha **Note, the package is not yet ready for usage but will be within the next few days.**

## Example 1

Creates a new instance of RakePlus, extract the phrases and return the results. Assumes that the specified
text is English (US).


```php

use DonatelloZa\RakePlus;

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

use DonatelloZa\RakePlus;

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

## Example 4

You can provide custom stopwords in three different ways:

```php

use DonatelloZa\RakePlus;

// 1: The standard way (provide a language code)
$rake = RakePlus::create($text, 'en_US');

// 2: Pass an array containing stopwords
$rake = RakePlus::create($text, ['a', 'able', 'about', 'above', ...]);

// 3: Pass the name of the PHP file that returns an array of stopwords, 
//    see lang/en_US.php
$rake = RakePlus::create($text, '/path/to/my/stopwords.php');

```

## License

Released under MIT license (read LICENSE).
