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

namespace Ajgl\Csv\Rfc\Spl;

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class SplTempFileObject extends \SplTempFileObject
{
    use SplFileObjectTrait;

    public function __construct(int $max_memory = null)
    {
        if (null === $max_memory) {
            parent::__construct();
        } else {
            parent::__construct($max_memory);
        }
        $this->fixCsvControl();
    }
}
