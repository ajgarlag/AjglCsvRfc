# [CHANGELOG](http://keepachangelog.com/)
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Changed
- Require PHP >=7.3 or >=8.0
- Upgrade php-cs-fixer dependency and fix-cs

## [0.3.0] - 2019-03-29
### Added
- Add `Ajgl\Csv\Rfc\CsvRfcUtils::fPutCsv` return value.
- Add `Ajgl\Csv\Rfc\fputcsv` return value.

## [0.2.0] - 2016-04-08
### Added
- New `Ajgl\Csv\Rfc\CsvRfcUtils::fixEnclosureEscape` method
- New stream filter `Ajgl\Csv\Rfc\CsvRfcWriteStreamFilter`

### Changed
- Remove the abstract declaration of `Ajgl\Csv\Rfc\CsvRfcUtils`
- Declare the `Ajgl\Csv\Rfc\CsvRfcUtils` constructor as private
- In writing context, the escape char should be a backslash (`\`), any other value will be ignored.

## 0.1.0 - 2016-02-25
### Added
- Add alternative implementation for CSV related functions:
  - `str_getcsv`
  - `fgetcsv`
  - `fputcsv`
- Add alternative implementation `SplFileObject` and `SplTempFileObject` to overwrite:
  - `SplFileObject::fgetcsv`
  - `SplFileObject::fputcsv`
  - `SplFileObject::setCsvControl`

[unreleased]: https://github.com/ajgarlag/AjglCsvRfc/compare/0.2.0...master
[0.2.0]: https://github.com/ajgarlag/AjglCsvRfc/compare/0.1.0...0.2.0
