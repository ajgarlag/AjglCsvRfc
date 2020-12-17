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

use function fgetcsv;
use function fputcsv;
use function str_getcsv;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CsvRfcUtils
{
    const EOL_WRITE_DEFAULT = "\n";
    const EOL_WRITE_RFC = "\r\n";

    private static $defaultEol = self::EOL_WRITE_DEFAULT;

    private function __construct()
    {
    }

    /**
     * @see http://php.net/manual/en/function.fputcsv.php
     *
     * @param resource $handle
     *
     * @return int|false the number of bytes written, or FALSE on error
     */
    public static function fPutCsv($handle, array $fields, string $delimiter = ',', string $enclosure = '"', ?string $escape = '\\', string $eol = null)
    {
        self::checkPutCsvEscape($escape);

        $eol = self::resolveEol($eol);
        if (self::EOL_WRITE_DEFAULT !== $eol || self::hasAnyValueWithEscapeFollowedByEnclosure($fields, $enclosure)) {
            return fwrite($handle, self::strPutCsv($fields, $delimiter, $enclosure, $eol));
        } else {
            return fputcsv($handle, $fields, $delimiter, $enclosure);
        }
    }

    private static function hasAnyValueWithEscapeFollowedByEnclosure(array $fields, string $enclosure): bool
    {
        foreach ($fields as $value) {
            if (false !== strpos($value, '\\'.$enclosure)) {
                return true;
            }
        }

        return false;
    }

    private static function resolveEol(?string $eol): string
    {
        return null === $eol ? self::$defaultEol : (string) $eol;
    }

    /**
     * @see http://php.net/manual/en/function.fgetcsv.php
     *
     * @param resource $handle
     *
     * @return array|false|null
     */
    public static function fGetCsv($handle, int $length = 0, string $delimiter = ',', string $enclosure = '"', string $escape = '"')
    {
        self::checkGetCsvEscape($enclosure, $escape);

        return fgetcsv($handle, $length, $delimiter, $enclosure, $enclosure);
    }

    /**
     * @see http://php.net/manual/en/function.str_getcsv.php
     */
    public static function strGetCsv(string $input, string $delimiter = ',', string $enclosure = '"', string $escape = '"'): array
    {
        self::checkGetCsvEscape($enclosure, $escape);

        return str_getcsv($input, $delimiter, $enclosure, $enclosure);
    }

    /**
     * This code was borrowed from goodby/csv under MIT LICENSE.
     *
     * @author Hidehito Nozawa <suinyeze@gmail.com>
     *
     * @see    https://github.com/goodby/csv
     * @see    https://github.com/goodby/csv/blob/c6677d9c68323ef734a67a34f3e5feabcafd5b4e/src/Goodby/CSV/Export/Standard/CsvFileObject.php#L46
     */
    public static function strPutCsv(array $fields, string $delimiter = ',', string $enclosure = '"', string $eol = self::EOL_WRITE_DEFAULT): string
    {
        $file = new \SplTempFileObject();
        $file->fputcsv($fields, $delimiter, $enclosure);
        $file->rewind();

        $line = '';
        while (!$file->eof()) {
            $line .= $file->fgets();
        }

        $line = self::fixEnclosureEscape($enclosure, $line);

        if (self::EOL_WRITE_DEFAULT !== $eol) {
            $line = rtrim($line, "\n").$eol;
        }

        return $line;
    }

    /**
     * Fix the enclosure escape in the given CSV raw line.
     */
    public static function fixEnclosureEscape(string $enclosure, string $line): string
    {
        return str_replace('\\'.$enclosure, '\\'.$enclosure.$enclosure, $line);
    }

    /**
     * Emits a warning if the escape char is not the default backslash or null.
     */
    public static function checkPutCsvEscape(?string $escape): void
    {
        if ('\\' !== $escape && null !== $escape) {
            trigger_error(
                sprintf(
                    "In writing mode, the escape char must be a backslash '\\'. "
                        ."The given escape char '%s' will be ignored.",
                    $escape
                ),
                E_USER_WARNING
            );
        }
    }

    /**
     * Emits a warning if the enclosure char and escape char are different.
     */
    public static function checkGetCsvEscape(string $enclosure, string $escape): void
    {
        if ($enclosure !== $escape) {
            trigger_error(
                sprintf(
                    'In reading mode, the escape and enclosure chars must be equals. '
                        ."The given escape char '%s' will be ignored.",
                    $escape
                ),
                E_USER_WARNING
            );
        }
    }

    public static function setDefaultWriteEol(string $eol): void
    {
        self::$defaultEol = $eol;
    }

    public static function getDefaultWriteEol(): string
    {
        return self::$defaultEol;
    }
}
