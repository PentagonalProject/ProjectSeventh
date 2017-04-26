<?php
/* ------------------------------------------------------ *\
 |                 CONTAINER ENVIRONMENT                  |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'environment'
 * Override Default Environment to prevent infinity loop & detect proxy from cdn.
 * (eg: CloudFlare CDN)
 */
namespace {

    use Slim\Http\Environment;

    /**
     * Environment Container
     *
     * @return Environment
     */
    return function () : Environment {
        $server = $_SERVER;
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'
            // hide behind proxy / maybe cloud flare cdn
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
            || !empty($_SERVER['HTTP_FRONT_END_HTTPS'])
            && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'
        ) {
            // detect if non standard protocol
            if ($_SERVER['SERVER_PORT'] == 80
                && (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                    || isset($_SERVER['HTTP_FRONT_END_HTTPS'])
                )
            ) {
                $server['SERVER_PORT'] = 443;
            }

            $server['HTTPS'] = 'on';
        }

        return new Environment($server);
    };
}
