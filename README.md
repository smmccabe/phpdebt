# phpdebt
PHP Technical Debt Calculator

It scans through your code looking for any faults based on a number of standards from existing code analysis tools, it totals up the number of faults it finds and compares them against the total lines of functional code (comments and whitespace are excluded) and gives a quality score.

Currently works primarly against Drupal

## Installation

```
wget https://github.com/smmccabe/phpdebt/releases/download/1.0.1/phpdebt.phar
chmod +x phpdebt.phar
sudo mv phpdebt.phar /usr/local/bin/phpdebt
```

## Usage and Examples

Against a whole project
```
phpdebt .
```

Against a specific folder
```
phpdebt src/
```

Against a specific file
```
phpdebt src/MyClass.php
```

Against wildcards
```
phpdebt src/*.inc
```

Sample output
```
phpdebt .
phpmd cleancode: 145
phpmd codesize: 19
phpmd design: 2
phpmd naming: 35
phpmd unusedcode: 43
phpcs Drupal: 39
phpcs DrupalPractice: 69
Total Faults: 352
Total Lines: 10568
Quality Score: 3 faults per 100 lines
```

### Score Guidelines

Faults per 100 lines:
* __< 2__ - Excellent Code
* __< 6__ - Good Code
* __< 10__ - Decent Code
* __< 25__ - Needs work, cleanup should be prioritized of most feature work
* __>= 25__ Needs significant work, cleanup should be prioritized over any feature work or bugfixes.

### Fixing Found Faults

phpdebt does not currently provide a verbose mode, to identify and fix specific faults, it is recommended to run tools such as phpmd and phpcs directly, see the phpdebt script if you wish to replicate what phpdebt is running. A verbose mode will be added in the future.

## Bugs or Issues

Please post any bugs, issues or support requests to the [github issue board](https://github.com/smmccabe/phpdebt/issues)