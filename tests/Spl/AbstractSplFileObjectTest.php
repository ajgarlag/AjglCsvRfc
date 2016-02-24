<?php

/*
 * AJGL CSV RFC Component
 *
 * Copyright (C) Antonio J. García Lagar <aj@garcialagar.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ajgl\Csv\Rfc\Tests\Spl;

use SplFileObject;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
abstract class AbstractSplFileObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SplFileObject
     */
    protected $fileObject;

    protected function setUp()
    {
        $this->fileObject = $this->buildFileObject();
    }

    public function testCsvIteration()
    {
        $this->fileObject->fwrite('"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n");
        $this->fileObject->rewind();
        $this->fileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
        $expected = array(array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"));
        $actual = array();
        foreach ($this->fileObject as $row) {
            $actual[] = $row;
        }
        $this->assertEquals($expected, $actual);
    }

    public function testFreadcsv()
    {
        $this->fileObject->fwrite('"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n");
        $this->fileObject->rewind();
        $this->assertEquals(array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"), $this->fileObject->fgetcsv());
    }

    public function testFputcsv()
    {
        $this->fileObject->fputcsv(array('Hello,World!', 'Hello;World!', 'Hello\"World\"!', "Hello\'World\'!", "Hello\nWorld!"));
        $this->fileObject->rewind();
        /* @todo drop with PHP5.4 */
        if (PHP_VERSION_ID < 50511) {
            $content = '';
            while (!$this->fileObject->eof()) {
                $content .= $this->fileObject->fgets();
            }
        } else {
            $content = $this->fileObject->fread(4096);
        }
        $this->assertEquals('"Hello,World!",Hello;World!,"Hello\""World\""!","Hello\\\'World\\\'!","Hello'."\n".'World!"'."\n", $content);
    }

    /**
     * @return \SplFileObject
     */
    abstract protected function buildFileObject();
}
