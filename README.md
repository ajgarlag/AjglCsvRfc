AjglCsvRfc
==========

The AjglCsvRfc component offers a drop in replacement for native PHP CSV related functions to read and/or write RFC4180
compliant CSV files.

The native PHP implementation contains a *Wont fix* bug [#50686] when you try to write a CSV field which contains the
escape char (`\` by default), followed by the enclosure char (`"` by default).

The RFC 4180 states that:
> If double-quotes are used to enclose fields, then a double-quote
> appearing inside a field must be escaped by preceding it with
> another double quote.

The CSV version of the string `Hello \"World"!` should be `"Hello \""World""!"` but it does not work as expected. You
can see a detailed explanation at https://3v4l.org/aROTj


License
-------

This component is under the MIT license. See the complete license in the LICENSE file.


Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker].


Author Information
------------------

Developed with ♥ by [Antonio J. García Lagar].

If you find this component useful, please add a ★ in the [GitHub repository page] and/or the [Packagist package page].

[#50686]: https://bugs.php.net/bug.php?id=50686
[Github issue tracker]: https://github.com/ajgarlag/AjglCsvRfc/issues
[Antonio J. García Lagar]: http://aj.garcialagar.es
[GitHub repository page]: https://github.com/ajgarlag/AjglCsvRfc
[Packagist package page]: https://packagist.org/packages/ajgl/csv-rfc
