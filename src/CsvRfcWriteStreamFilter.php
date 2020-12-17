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

class CsvRfcWriteStreamFilter extends \php_user_filter
{
    const FILTERNAME_DEFAULT = 'csv.rfc.write';

    private $filternameEnclosure = '"';

    public static function register(string $filtername = self::FILTERNAME_DEFAULT): bool
    {
        return stream_filter_register($filtername, 'Ajgl\Csv\Rfc\CsvRfcWriteStreamFilter');
    }

    public function onCreate(): bool
    {
        $this->extractEnclosureFromFilternameIfAvailable();

        return true;
    }

    private function extractEnclosureFromFilternameIfAvailable(): void
    {
        if (\strlen($this->filtername) === \strlen(self::FILTERNAME_DEFAULT) + 2 && 0 === strpos($this->filtername, self::FILTERNAME_DEFAULT.'.')) {
            $this->filternameEnclosure = substr($this->filtername, -1);
        }
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     *
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        $enclosure = $this->resolveEnclosure();

        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = CsvRfcUtils::fixEnclosureEscape($enclosure, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

    private function resolveEnclosure(): string
    {
        if (\is_array($this->params) && isset($this->params['enclosure']) && 1 === \strlen($this->params['enclosure'])) {
            return $this->params['enclosure'];
        }

        return $this->filternameEnclosure;
    }
}
