<?php
/* ------------------------------------------------------ *\
 |                       CONSTANT                          |
\* ------------------------------------------------------ */

/* SERVER TYPE CONSTANT
 * ----------------------------- */
define('SERVER_TYPE_APACHE', 'apache');
define('SERVER_TYPE_LITESPEED', 'litespeed');
define('SERVER_TYPE_NGINX', 'nginx');
define('SERVER_TYPE_IIS', 'iis');
define('SERVER_TYPE_IIS7', 'iis7');
define('SERVER_TYPE_HIAWATHA', 'hiawatha');
define('SERVER_TYPE_LIGHTTPD', 'lighttpd');
define('SERVER_TYPE_UNKNOWN', '');

/* GLOBAL CONTAINER NAME CONSTANT
 * ----------------------------- */
define('CONTAINER_APPLICATION', \PentagonalProject\ProjectSeventh\Application::APP_KEY);
define('CONTAINER_APP', CONTAINER_APPLICATION);

define('CONTAINER_CONFIG', 'config');
define('CONTAINER_CONFIGS', CONTAINER_CONFIG);

define('CONTAINER_DATABASE', 'database');
define('CONTAINER_DATABASES', CONTAINER_DATABASE);

define('CONTAINER_ENVIRONMENT', 'environment');
define('CONTAINER_ENVIRONMENTS', CONTAINER_ENVIRONMENT);

define('CONTAINER_HOOK', 'hook');
define('CONTAINER_HOOKS', CONTAINER_HOOK);

define('CONTAINER_MODULE', 'module');
define('CONTAINER_MODULES', CONTAINER_MODULE);

define('CONTAINER_EXTENSION', 'extension');
define('CONTAINER_EXTENSIONS', CONTAINER_EXTENSION);

define('CONTAINER_SETTING', 'settings');
define('CONTAINER_SETTINGS', CONTAINER_SETTING);

define('CONTAINER_SESSION', 'session');
define('CONTAINER_SESSIONS', CONTAINER_SESSION);

define('CONTAINER_SLIM', 'slim');
define('CONTAINER_REQUEST', 'request');
define('CONTAINER_RESPONSE', 'response');
define('CONTAINER_ROUTER', 'router');
define('CONTAINER_NOT_ALLOWED_HANDLER', 'notAllowedHandler');
define('CONTAINER_ERROR_HANDLER', 'errorHandler');
define('CONTAINER_NOT_FOUND_HANDLER', 'notFoundHandler');
define('CONTAINER_FOUND_HANDLER', 'foundHandler');
define('CONTAINER_PHP_ERROR_HANDLER', 'phpErrorHandler');
define('CONTAINER_CALLABLE_RESOLVER', 'callableResolver');

// add empty for helper Auto Complete IDE
define('CONTAINER_', '');


/* GLOBAL HOOK CONSTANT
 * ----------------------------- */
define('HOOK_HANDLER_ERROR', 'Handler:Error');

define('HOOK_HANDLER_ERROR_404', HOOK_HANDLER_ERROR . ':404');
define('HOOK_HANDLER_ERROR_NOT_FOUND', HOOK_HANDLER_ERROR_404);

define('HOOK_HANDLER_ERROR_500', HOOK_HANDLER_ERROR . ':500');
define('HOOK_HANDLER_ERROR_EXCEPTION', HOOK_HANDLER_ERROR_500);

define('HOOK_HANDLER_ERROR_403', HOOK_HANDLER_ERROR . ':403');
define('HOOK_HANDLER_ERROR_NOT_ALLOWED', HOOK_HANDLER_ERROR_403);

define('HOOK_HANDLER_ERROR_PHP', HOOK_HANDLER_ERROR . ':Php');
define('HOOK_HANDLER_CALLABLE_RESOLVER', 'Handler:Callable:Resolver');
define('HOOK_HANDLER_FOUND_RESPONSE', 'Handler:Found:Response');
