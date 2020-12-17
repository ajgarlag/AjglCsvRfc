AjglCsvRfc
==========

The AjglCsvRfc component offers a drop in replacement for native PHP CSV related functions to read and/or write RFC4180
compliant CSV files.

[![Build Status](https://github.com/ajgarlag/AjglCsvRfc/workflows/test/badge.svg?branch=master)](https://github.com/ajgarlag/AjglCsvRfc/actions)
[![Latest Stable Version](https://poser.pugx.org/ajgl/csv-rfc/v/stable.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Latest Unstable Version](https://poser.pugx.org/ajgl/csv-rfc/v/unstable.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Total Downloads](https://poser.pugx.org/ajgl/csv-rfc/downloads.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Montly Downloads](https://poser.pugx.org/ajgl/csv-rfc/d/monthly.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![Daily Downloads](https://poser.pugx.org/ajgl/csv-rfc/d/daily.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![License](https://poser.pugx.org/ajgl/csv-rfc/license.png)](https://packagist.org/packages/ajgl/csv-rfc)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7218debc-6c07-4a60-9b0b-e08103c1e0b2/mini.png)](https://insight.sensiolabs.com/projects/7218debc-6c07-4a60-9b0b-e08103c1e0b2)

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
use Ajgl\Csv\Rfc;

$file = new Rfc\Spl\SplFileObject('php://temp', 'w+');
$file->fputcsv(array('Hello \"World"!'));
$file->rewind();
$row = $file->fgetcsv();
$file->rewind();
$file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);
foreach ($file as $line) {
    $row = $line;
}
```

### Stream filter

Instead of using the alternative functions or classes, you can use the provided stream filter to fix the enclosure
escape. You must register the stream filter (if not registered yet) and append it to your stream:
```php
use Ajgl\Csv\Rfc;

Rfc\CsvRfcWriteStreamFilter::register();

$handler = fopen('php://temp', 'w+');
stream_filter_append(
    $handler,
    Rfc\CsvRfcWriteStreamFilter::FILTERNAME_DEFAULT,
    STREAM_FILTER_WRITE
);
fputcsv($handler, array('Hello \"World"!'));
rewind($handler);
$row = fgetcsv($handler, 0, ',', '"', '"');
```
**❮ NOTE ❯**: The `$escape_char` in [fputcsv] MUST be (if allowed) the default one (the backslash `\`). The `$enclosure`
and `$escape` parameters in [fgetcsv] MUST be equals.

#### Custom enclosure character

By default, the enclosure character of the stream filter is a double-quote (`"`). If you want to change it, you can
provide a custom enclosure character in two different ways.

##### Via filter params

An array with an `enclosure` key can be provided when appending the filter to the stream:

```php
use Ajgl\Csv\Rfc;

$enclosure = '@';
Rfc\CsvRfcWriteStreamFilter::register();

$handler = fopen('php://temp', 'w+');
stream_filter_append(
    $handler,
    Rfc\CsvRfcWriteStreamFilter::FILTERNAME_DEFAULT,
    STREAM_FILTER_WRITE,
    array(
        'enclosure' => $enclosure
    )
);
fputcsv($handler, array('Hello \"World"!'), ',', '@');
rewind($handler);
$row = fgetcsv($handler, 0, ',', '@', '@');
```

##### Via filter name

If the filter name starts with the special key `csv.rfc.write.` you can define your custom enclosure character appending
it to the filtername:

```php
use Ajgl\Csv\Rfc;

$enclosure = '@';
$filtername = 'csv.rfc.write.' . $enclosure;
Rfc\CsvRfcWriteStreamFilter::register($filtername);

$handler = fopen('php://temp', 'w+');
stream_filter_append(
    $handler,
    $filtername,
    STREAM_FILTER_WRITE
);
fputcsv($handler, array('Hello \"World"!'), ',', '@');
rewind($handler);
$row = fgetcsv($handler, 0, ',', '@', '@');
```

**❮ NOTE ❯**: The enclosure character passed via parameters will override the one defined via filter name.


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
problem with the EOL detection, you should enable the `auto_detect_line_endings` configuration option as following the
PHP doc [recomendation](https://secure.php.net/manual/en/filesystem.configuration.php#ini.auto-detect-line-endings).
```php
ini_set('ini.auto-detect-line-endings', true);
```


### Integration with `league/csv <9.0`

The well known [`league/csv`](http://csv.thephpleague.com/) package provide a great object oriented API to work with CSV
data, but as long as it leverages the default PHP implementation for CSV, versions prior to 9.0 are affected by the [#50686] bug.

You can use this component with `league/csv <9.0` to produce [RFC 4180] compatible files avoiding this bug.


#### Writer integration

To integrate this component with the `league/csv` writer implementation, you can use the Stream Filter API.

```php
use Ajgl\Csv\Rfc;
use League\Csv\Writer;

CsvRfcWriteStreamFilter::register();
$writer = Writer::createFromPath('/tmp/foobar.csv');
if (!$writer->isActiveStreamFilter()) {
    throw new \Exception("The Stream Filter API is not active.");
}
$writer->appendStreamFilter(CsvRfcWriteStreamFilter::FILTERNAME_DEFAULT);
$writer->insertOne(array('"Hello\", World!'));
```
**❮ NOTE ❯**: Do not override the default writer escape character (`\`).

##### Known limitations
 * The `league/csv` package does not support the Stream Filter API when the writer instance is created from a
`SplFileObject`.
 * The `league/csv` implementation will always leverage the standard `\SplFileObject::fputcsv` to write CSV data, so the
fix to write [RFC 4180] compatible files from `Ajgl\Csv\Rfc\Spl\SplFileObject::fputcsv` will be ignored.


#### Reader Integration

To read back the CSV data, you can leverage the standard implementation, but you MUST set the reader escape and
enclosure characters to the same value.

```php
use League\Csv\Reader;

$reader = Reader::createFromPath('/tmp/foobar.csv');
$reader->setEscape($reader->getEnclosure());
foreach ($reader as $row) {
    //...
}
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
[fgetcsv]: http://php.net/manual/en/function.fgetcsv.php
[fputcsv]: http://php.net/manual/en/function.fputcsv.php
[LICENSE]: LICENSE
[Github issue tracker]: https://github.com/ajgarlag/AjglCsvRfc/issues
[Antonio J. García Lagar]: http://aj.garcialagar.es
[GitHub repository page]: https://github.com/ajgarlag/AjglCsvRfc
[Packagist package page]: https://packagist.org/packages/ajgl/csv-rfc
