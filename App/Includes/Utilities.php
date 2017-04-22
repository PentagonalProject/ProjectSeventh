<?php
/* ------------------------------------------------------ *\
 |                      UTILITIES                         |
\* ------------------------------------------------------ */

/**
 * @param string $file
 */
function IncludeFileOnce(string $file)
{
    /** @noinspection PhpIncludeInspection */
    include_once $file;
}
