AjglCsvRfc
==========

The AjglCsvRfc component offers a drop in replacement for native PHP CSV related functions to read and/or write RFC4180
compliant CSV files.

[![Build Status](https://travis-ci.org/ajgarlag/AjglCsvRfc.png?branch=master)](https://travis-ci.org/ajgarlag/AjglCsvRfc)
[![Latest Stable Version](https://poser.pugx.org/ajgl/csv-rfc/v/stable.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Latest Unstable Version](https://poser.pugx.org/ajgl/csv-rfc/v/unstable.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Total Downloads](https://poser.pugx.org/ajgl/csv-rfc/downloads.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Montly Downloads](https://poser.pugx.org/ajgl/csv-rfc/d/monthly.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Daily Downloads](https://poser.pugx.org/ajgl/csv-rfc/d/daily.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![License](https://poser.pugx.org/ajgl/csv-rfc/license.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ajgarlag/AjglCsvRfc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ajgarlag/AjglCsvRfc/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ajgarlag/AjglCsvRfc/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ajgarlag/AjglCsvRfc/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7218debc-6c07-4a60-9b0b-e08103c1e0b2/mini.png)](https://insight.sensiolabs.com/projects/7218debc-6c07-4a60-9b0b-e08103c1e0b2)
[![StyleCI](https://styleci.io/repos/52462082/shield)](https://styleci.io/repos/52462082)

The native PHP implementation contains a *Wont fix* bug [#50686] when you try to write a CSV field which contains the
escape char (`\` by default), followed by the enclosure char (`"` by default).

The [RFC 4180] states that:
> If double-quotes are used to enclose fields, then a double-quote
> appearing inside a field must be escaped by preceding it with
> another double quote.

The CSV version of the string `"Hello\", World!` should be `"""Hello\"", World!"` but it does not work as expected. You
can see a detailed explanation at https://3v4l.org/NnHp4

This package provides an alternative implementation to read and write well escaped CSV files for the following functions
and methods:

| Native | Alternative |
| ------ | ----------- |
| `fgetcsv`  | `Ajgl\Csv\Rfc\fgetcsv`  |
| `fputcsv`  | `Ajgl\Csv\Rfc\fputcsv`  |
| `str_getcsv`  | `Ajgl\Csv\Rfc\str_getcsv`  |
| `SplFileObject::fgetcsv`  | `Ajgl\Csv\Rfc\Spl\SplFileObject::fgetcsv`  |
| `SplFileObject::fputcsv`  | `Ajgl\Csv\Rfc\Spl\SplFileObject::fputcsv`  |
| `SplFileObject::setCsvControl`  | `Ajgl\Csv\Rfc\Spl\SplFileObject::setCsvControl`  |


Installation
------------

To install the latest stable version of this component, open a console and execute the following command:
```
$ composer require ajgl/csv-rfc
```


Usage
-----

### Alternative functions

The simplest way to use this library is to call the alternative CSV functions:
```php
use Ajgl\Csv\Rfc;

$handler = fopen('php://temp', 'w+');
Rfc\fputcsv($handler, array('Hello \"World"!'));
rewind($handler);
$row = Rfc\fgetcsv($handler);
rewind($handler);
$row = Rfc\str_getcsv(fgets($handler));
```

### Alternative clases

If you prefer you can use the alternative implementation for `SplFileObject` or `SplTempFileObject`:
```php
$file = new Ajgl\Csv\Rfc\Spl\SplFileObject('php://temp', 'w+');
$file->fputcsv(array('Hello \"World"!'));
$file->rewind();
$row = $file->fgetcsv();
$file->rewind();
$file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
foreach ($file as $line) {
    $row = $line;
}
```

### End of line (EOL)

#### Writing CSV
By default, the PHP CSV implementation uses `LF` (`"\n"`) as EOL while writing a CSV row. These alternative functions
use `LF` (`"\n"`) by default too.

But, the RFC 4180 states that:
> Each record is located on a separate line, delimited by a line
> break (CRLF).

So, if you want to write RFC4180 compliant CSV, you can override the default EOL using:
```php
use Ajgl\Csv\Rfc\CsvRfcUtils;

CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_RFC);
```

#### Reading CSV
To read the CSV data, this implementation leverages the PHP native capabilities to read files. If you are having any
problem with this, you should enable the `auto_detect_line_endings` configuration option as following the PHP doc
[recomendation](https://secure.php.net/manual/en/filesystem.configuration.php#ini.auto-detect-line-endings).
```php
ini_set('ini.auto-detect-line-endings', true);
```


License
-------

This component is under the MIT license. See the complete license in the [LICENSE] file.


Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker].


Author Information
------------------

Developed with ♥ by [Antonio J. García Lagar].

If you find this component useful, please add a ★ in the [GitHub repository page] and/or the [Packagist package page].

[#50686]: https://bugs.php.net/bug.php?id=50686
[RFC 4180]: https://tools.ietf.org/html/rfc4180
[LICENSE]: LICENSE
[Github issue tracker]: https://github.com/ajgarlag/AjglCsvRfc/issues
[Antonio J. García Lagar]: http://aj.garcialagar.es
[GitHub repository page]: https://github.com/ajgarlag/AjglCsvRfc
[Packagist package page]: https://packagist.org/packages/ajgl/csv-rfc
