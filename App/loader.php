<?php
/* ------------------------------------------------------ *\
 |                         LOADER                         |
\* ------------------------------------------------------ */

namespace Pentagonal {

    use Pentagonal\ProjectApi\AutoLoaderClass;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'AutoLoaderClass.php';
    // register
    AutoLoaderClass::createRegister(
        'Pentagonal\\ProjectApi\\',
        __DIR__ . '/Classes'
    );
}
