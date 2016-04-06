<?php

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\Csv\Rfc\Tests;

use Ajgl\Csv\Rfc\CsvRfcUtils;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class CsvRfcUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testStrGetCsv()
    {
        $expected = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $this->assertEquals(
            $expected,
            CsvRfcUtils::strGetCsv('"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n")
        );
        $this->assertEquals(
            $expected,
            CsvRfcUtils::strGetCsv('Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n", ';')
        );
        $this->assertEquals(
            $expected,
            CsvRfcUtils::strGetCsv("Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n", ';', "'", "'")
        );
    }

    public function testFGetCsv()
    {
        $expected = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            CsvRfcUtils::fGetCsv($fp)
        );

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, 'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            CsvRfcUtils::fGetCsv($fp, 0, ';')
        );

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            CsvRfcUtils::fGetCsv($fp, 0, ';', "'", "'")
        );
    }

    public function testStrPutCsv()
    {
        $fields = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");
        $this->assertEquals(
            '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n",
            CsvRfcUtils::strPutCsv($fields)
        );
        $this->assertEquals(
            'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n",
            CsvRfcUtils::strPutCsv($fields, ';')
        );
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n",
            CsvRfcUtils::strPutCsv($fields, ';', "'")
        );
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\r\n",
            CsvRfcUtils::strPutCsv($fields, ';', "'", CsvRfcUtils::EOL_WRITE_RFC)
        );
    }

    public function testFPutCsv()
    {
        $fields = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields);
        rewind($fp);
        $this->assertEquals(
            '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields, ';');
        rewind($fp);
        $this->assertEquals(
            'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields, ';', "'", "'");
        rewind($fp);
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields, ';', "'", "'", CsvRfcUtils::EOL_WRITE_RFC);
        rewind($fp);
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\r\n",
            fread($fp, 4096)
        );
    }

    public function testDefaultEol()
    {
        $this->assertEquals("\n", CsvRfcUtils::getDefaultWriteEol());
        $this->assertNull(CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_RFC));
        $this->assertEquals(CsvRfcUtils::EOL_WRITE_RFC, CsvRfcUtils::getDefaultWriteEol());
        $this->assertNull(CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_DEFAULT));
        $this->assertEquals("\n", CsvRfcUtils::getDefaultWriteEol());
    }

    public function testFPutCsvWithRfc()
    {
        $fields = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_RFC);

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields);
        rewind($fp);
        $this->assertEquals(
            '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\r\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields, ';');
        rewind($fp);
        $this->assertEquals(
            'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\r\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        CsvRfcUtils::fPutCsv($fp, $fields, ';', "'", "'");
        rewind($fp);
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\r\n",
            fread($fp, 4096)
        );

        CsvRfcUtils::setDefaultWriteEol(CsvRfcUtils::EOL_WRITE_DEFAULT);
    }

    public function testFixEnclosureEscape()
    {
        $payload = '""Hello\", World!';
        $expected = '""Hello\"", World!';
        $actual = CsvRfcUtils::fixEnclosureEscape('"', $payload);
        $this->assertEquals($expected, $actual);
    }
}
