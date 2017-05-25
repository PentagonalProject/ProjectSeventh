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

/**
 * Get Alias Log Level
 *
 * @param int $code
 * @return int if no match between code will be return int 0
 */
function getAliasLogLevel(int $code) : int
{
    switch ($code) {
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            return \Monolog\Logger::NOTICE;
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
            return \Monolog\Logger::ERROR;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
            return \Monolog\Logger::WARNING;
        case E_ALL:
            return \Monolog\Logger::DEBUG;
    }

    return 0;
}

/**
 * Get Default Log Name By Code
 *
 * @param int $code
 * @param string $default
 * @return string
 */
function getDefaultLogNameByCode($code, string $default = 'logs.log') : string
{
    $code = is_numeric($code) && !is_float($code)
        ? abs($code)
        : $code;
    if (!is_int($code)) {
        return $default;
    }

    switch (getAliasLogLevel($code)) {
        case \Monolog\Logger::NOTICE:
            return 'notice.log';
        case \Monolog\Logger::ERROR:
            return 'error.log';
        case \Monolog\Logger::WARNING:
            return 'warning.log';
        case \Monolog\Logger::INFO:
            return 'info.log';
        case \Monolog\Logger::DEBUG:
            return 'debug.log';
        case \Monolog\Logger::EMERGENCY:
            return 'emergency.log';
        case \Monolog\Logger::CRITICAL:
            return 'critical.log';
        case \Monolog\Logger::ALERT;
            return 'alert.log';
    }

    return $default;
}
