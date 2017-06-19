[![Build Status](https://travis-ci.org/hedii/uptime-checker.svg?branch=master)](https://travis-ci.org/hedii/uptime-checker)

# Uptime Checker

A php library to monitor website uptime

## Table of contents

- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Usage](#usage)
  - [Instantiation](#instantiation)
  - [Perform an uptime check](#perform-an-uptime-check)
  - [The result array](#the-result-array)
- [Testing](#testing)
- [License](#license)

## Installation

Install via [composer](https://getcomposer.org/doc/00-intro.md)
```sh
composer require hedii/uptime-checker
```

## Usage

### Instantiation

Create a uptime checker instance:

```php
<?php

// require composer autoloader
require '/path/to/vendor/autoload.php';

// instantiate
$checker = new Hedii\UptimeChecker\UptimeChecker();
```

Alternatively, you can pass a GuzzleHttp\Client instance as a parameter if you want to set your own http client options ([see Guzzle documentation](http://docs.guzzlephp.org/en/latest/request-options.html)):

```php
// instantiate with the http client as a parameter
$checker = new Hedii\UptimeChecker\UptimeChecker(new Client([
    'delay' => 1000,
    'allow_redirects' => false
]));
```

### Perform an uptime check

Call the `check($url)` method with an url as a parameter to perform the uptime check.

```php
$checker = new Hedii\UptimeChecker\UptimeChecker());
$result = $checker->check('http://example.com');
```

The result of this method is an array with with the check report information. The value of `success` indicates if the website is up or down:

```
array(5) {
    'uri' => "http://example.com"
    'success' => true
    'status' => 200
    'message' => "OK"
    'transfer_time' => 0.765217
}
```

### The result array

| Field           | Type    | Description                                           |
| --------------- | ------- | ----------------------------------------------------- |
| `uri`           | string  | The url to test                                       |
| `success`       | boolean | Whether the uptime test is successful or not          |
| `status`        | integer | The http response status code                         |
| `message`       | string  | The http response message or the client error message |
| `transfer_time` | float   | The transfer time in seconds                          |

## Testing

```
composer test
```

## License

hedii/uptime-checker is released under the MIT Licence. See the bundled [LICENSE](https://github.com/hedii/uptime-checker/blob/master/LICENSE.md) file for details.
