<?php

declare(strict_types=1);

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\Csv\Rfc;

/**
 * @see http://php.net/manual/en/function.fputcsv.php
 *
 * @param resource $handle
 *
 * @return int|false the number of bytes written, or FALSE on error
 */
function fputcsv($handle, array $fields, string $delimiter = ',', string $enclosure = '"', string $escape = '\\')
{
    return CsvRfcUtils::fPutCsv($handle, $fields, $delimiter, $enclosure, $escape);
}

/**
 * @see http://php.net/manual/en/function.fgetcsv.php
 *
 * @param resource $handle
 *
 * @return array|false|null
 */
function fgetcsv($handle, int $length = 0, string $delimiter = ',', string $enclosure = '"', string $escape = '"')
{
    return CsvRfcUtils::fGetCsv($handle, $length, $delimiter, $enclosure, $escape);
}

/**
 * @see http://php.net/manual/en/function.str_getcsv.php
 */
function str_getcsv(string $input, string $delimiter = ',', string $enclosure = '"', string $escape = '"'): array
{
    return CsvRfcUtils::strGetCsv($input, $delimiter, $enclosure, $escape);
}
