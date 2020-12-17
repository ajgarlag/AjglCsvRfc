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

namespace Ajgl\Rfc\Rfc\Tests;

use Ajgl\Csv\Rfc;
use PHPUnit\Framework\TestCase;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class functionsTest extends TestCase
{
    public function testStrGetcsv(): void
    {
        $expected = ['Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"];

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

    public function testFgetcsv(): void
    {
        $expected = ['Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"];

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

    public function testFputcsvData(): void
    {
        $fields = ['Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"];

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

    public function testFputcsvReturnValue(): void
    {
        $fields = ['a'];

        $fp = fopen('php://temp', 'w+');
        $result = Rfc\fputcsv($fp, $fields);
        rewind($fp);
        $this->assertEquals(
            'a'."\n",
            fread($fp, 4096)
        );
        $this->assertEquals(2, $result);
    }
}
