<?php
/* ------------------------------------------------------ *\
 |                      BOOTSTRAP                         |
\* ------------------------------------------------------ */
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    return;
}

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'loader.php';
