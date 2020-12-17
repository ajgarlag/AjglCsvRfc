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

namespace Ajgl\Csv\Rfc\Tests\Spl;

use Ajgl\Csv\Rfc\Spl\SplFileObject;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class SplFileObjectTest extends AbstractSplFileObjectTest
{
    protected function buildFileObject()
    {
        return new SplFileObject('php://temp', 'w+');
    }
}
