<?php

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\Csv\Rfc\Spl;

use Ajgl\Csv\Rfc\CsvRfcUtils;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
trait SplFileObjectTrait
{
    abstract public function getCsvControl();

    abstract public function fwrite($str, $length = null);

    protected function fixCsvControl()
    {
        list($delimiter, $enclosure) = $this->getCsvControl();
        $this->setCsvControl($delimiter, $enclosure);
    }

    /**
     * @param string $enclosure
     * @param string $escape
     */
    protected function checkEscapeChar($enclosure, $escape)
    {
        CsvRfcUtils::checkCsvEscape($enclosure, $escape);
    }

    public function fgetcsv($delimiter = ',', $enclosure = '"', $escape = '"')
    {
        $this->checkEscapeChar($enclosure, $escape);

        return parent::fgetcsv($delimiter, $enclosure, $enclosure);
    }

    public function fputcsv($fields, $delimiter = ',', $enclosure = '"', $escape = '"')
    {
        $this->checkEscapeChar($enclosure, $escape);
        $this->fwrite(CsvRfcUtils::strPutCsv($fields, $delimiter, $enclosure));
    }

    public function setCsvControl($delimiter = ',', $enclosure = '"', $escape = '"')
    {
        $this->checkEscapeChar($enclosure, $escape);
        parent::setCsvControl($delimiter, $enclosure, $enclosure);
    }
}
