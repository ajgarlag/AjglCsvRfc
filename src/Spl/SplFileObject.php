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

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class SplFileObject extends \SplFileObject
{
    use SplFileObjectTrait;

    public function __construct($filename, $open_mode = 'r', $use_include_path = false, $context = null)
    {
        /* @todo drop with PHP5.4 */
        if (PHP_VERSION_ID < 50505 && $context === null) {
            parent::__construct($filename, $open_mode, $use_include_path);
        } else {
            parent::__construct($filename, $open_mode, $use_include_path, $context);
        }
        $this->fixCsvControl();
    }
}
