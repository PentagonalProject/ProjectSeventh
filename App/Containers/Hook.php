<?php
/* ------------------------------------------------------ *\
 |                    CONTAINER HOOK                      |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'hook'
 * Handle hook able object storage
 */
namespace {

    use PentagonalProject\ProjectSeventh\Hook;

    /**
     * @return Hook
     */
    return function () : Hook {
        return new Hook();
    };
}
