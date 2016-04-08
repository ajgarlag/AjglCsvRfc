<?php

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\Rfc\Rfc\Tests;

use Ajgl\Csv\Rfc;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class functionsTest extends \PHPUnit_Framework_TestCase
{
    public function test_str_getcsv()
    {
        $expected = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $this->assertEquals(
            $expected,
            Rfc\str_getcsv('"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n")
        );
        $this->assertEquals(
            $expected,
            Rfc\str_getcsv('Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n", ';')
        );
        $this->assertEquals(
            $expected,
            Rfc\str_getcsv("Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n", ';', "'", "'")
        );
    }

    public function test_fgetcsv()
    {
        $expected = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            Rfc\fgetcsv($fp)
        );

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, 'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            Rfc\fgetcsv($fp, 0, ';')
        );

        $fp = fopen('php://temp', 'w+');
        fwrite($fp, "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n");
        rewind($fp);
        $this->assertEquals(
            $expected,
            Rfc\fgetcsv($fp, 0, ';', "'", "'")
        );
    }

    public function test_fputcsv()
    {
        $fields = array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!");

        $fp = fopen('php://temp', 'w+');
        Rfc\fputcsv($fp, $fields);
        rewind($fp);
        $this->assertEquals(
            '"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        Rfc\fputcsv($fp, $fields, ';');
        rewind($fp);
        $this->assertEquals(
            'Hello,World!;"Hello;World!";"Hello\""World\""!";"Hello\\\'World\\\'!";"Hello'."\n".'World!"'."\n",
            fread($fp, 4096)
        );

        $fp = fopen('php://temp', 'w+');
        Rfc\fputcsv($fp, $fields, ';', "'");
        rewind($fp);
        $this->assertEquals(
            "Hello,World!;'Hello;World!';'Hello\\\"World\\\"!';'Hello\''World\''!';'Hello\nWorld!'\n",
            fread($fp, 4096)
        );
    }
}
