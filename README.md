# rake-php-plus
Yet another PHP implementation of the Rapid Automatic Keyword Extraction algorithm (RAKE).

This project is based on another project: [RAKE-PHP](https://github.com/Richdark/RAKE-PHP) by Richard Filipčík, which
in turn was based from a Python implementation: [RAKE](https://github.com/aneesha/RAKE)

As described in:

ROSE, Stuart, et al. Automatic keyword extraction from individual documents. Text Mining, 2010, 1-20.

This particular package intends to:

1. Add [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
2. Implement [PSR-4](http://www.php-fig.org/psr/psr-4/) in order to be [Composer](https://getcomposer.org) compatible.
3. Add additional functionality such as method chaining.

## Version

0.1

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

// Sort in descending order
$phrases = $rake->sort('desc')->get();
print_r($phrases);

// Sort the phrases by score and return the scores
$phrase_scores = $rake->sortByScore('desc')->scores();
print_r($phrase_scores);

// Extract phrases from a new string. Using the same RakePlus instance to extract the
// phrases from a string is faster than creating a new instance as the
// language files do not have to be re-loaded and parsed.
$phrases = $rake->extract("A fast Fourier transform (FFT) algorithm computes...")->sort()->get();
print_r($phrases);

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

## License

Released under MIT license (read LICENSE).
