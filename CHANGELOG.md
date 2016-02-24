# [CHANGELOG](http://keepachangelog.com/)
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased
### Added
- Add alternative implementation for CSV related functions:
  - `str_getcsv`
  - `fgetcsv`
  - `fputcsv`
- Add alternative implementation `SplFileObject` and `SplTempFileObject` to overwrite:
  - `SplFileObject::fgetcsv`
  - `SplFileObject::fputcsv`
  - `SplFileObject::setCsvControl`
