<?php
/* ------------------------------------------------------ *\
 |                      ENVIRONMENT                       |
\* ------------------------------------------------------ */

/**
 * Get Web Server Type
 *
 * @return string
 */
function getWebServerSoftWare() : string
{
    static $type;

    if (isset($type)) {
        return $type;
    }

    $software = isset($_SERVER['SERVER_SOFTWARE'])
        ? $_SERVER['SERVER_SOFTWARE']
        : null;

    $type = SERVER_TYPE_UNKNOWN;

    if (stripos($software, 'lighttpd') !== false) {
        $type = SERVER_TYPE_LIGHTTPD;
    }

    if (strpos($software, 'Hiawatha') !== false) {
        $type = SERVER_TYPE_HIAWATHA;
    }

    if (strpos($software, 'Apache') !== false) {
        $type = SERVER_TYPE_APACHE;
    } elseif (strpos($software, 'Litespeed') !== false) {
        $type = SERVER_TYPE_LITESPEED;
    }

    if (strpos($software, 'nginx') !== false) {
        $type = SERVER_TYPE_NGINX;
    }

    if ($type !== SERVER_TYPE_APACHE && $type !== SERVER_TYPE_LITESPEED
        && strpos($software, 'Microsoft-IIS') !== false
        && strpos($software, 'ExpressionDevServer') !== false
    ) {
        $type = SERVER_TYPE_IIS;
        if (intval(substr($software, strpos($software, 'Microsoft-IIS/')+14)) >= 7) {
            $type =  SERVER_TYPE_IIS7;
        }
    }
    if (function_exists('apache_get_modules')) {
        if (in_array('mod_security', apache_get_modules())) {
            $type = SERVER_TYPE_APACHE;
        }

        if ($type == SERVER_TYPE_UNKNOWN && function_exists('apache_get_version')
        ) {
            $type = SERVER_TYPE_APACHE;
        }
    }

    return $type;
}

/**
 * Check if apache Server Base
 *
 * @return bool
 */
function isApache() : bool
{
    return in_array(getWebServerSoftWare(), [SERVER_TYPE_APACHE, SERVER_TYPE_LITESPEED]);
}

/**
 * Check if Litespeed Web Server Base
 *
 * @return bool
 */
function isLiteSpeed(): bool
{
    return getWebServerSoftWare() === SERVER_TYPE_LIGHTTPD;
}

/**
 * Check if Nginx Web Server Base
 *
 * @return bool
 */
function isNginx(): bool
{
    return getWebServerSoftWare() === SERVER_TYPE_NGINX;
}

/**
 * Check if Hiawatha Web Server Base
 *
 * @return bool
 */
function isHiawatha(): bool
{
    return getWebServerSoftWare() === SERVER_TYPE_HIAWATHA;
}

/**
 * Check if Hiawatha Web Server Base
 *
 * @return bool
 */
function isLighttpd() : bool
{
    return getWebServerSoftWare() === SERVER_TYPE_LIGHTTPD;
}

/**
 * Check if IIS Web Server Base
 *
 * @return bool
 */
function isIIS() : bool
{
    return in_array(getWebServerSoftWare(), [SERVER_TYPE_IIS, SERVER_TYPE_IIS7]);
}

/**
 * Check if IIS7 Web Server Base
 *
 * @return bool
 */
function isIIS7() : bool
{
    return getWebServerSoftWare() === SERVER_TYPE_IIS7;
}

/**
 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
 *
 * @return bool
 */
function isMobile() : bool
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($userAgent, 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
        || strpos($userAgent, 'Android') !== false
        || strpos($userAgent, 'Silk/') !== false
        || strpos($userAgent, 'Kindle') !== false
        || strpos($userAgent, 'BlackBerry') !== false
        || strpos($userAgent, 'Opera Mini') !== false
        || strpos($userAgent, 'Opera Mobi') !== false
    ) {
        return true;
    }

    return false;
}
