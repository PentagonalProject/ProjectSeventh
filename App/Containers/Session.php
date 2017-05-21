<?php
/* ------------------------------------------------------ *\
 |                   CONTAINER SESSION                    |
\* ------------------------------------------------------ */

/**
 * File for Slim Container for 'session'
 * Handle hook able object storage
 */
namespace {

    use PentagonalProject\ProjectSeventh\Config;
    use PentagonalProject\ProjectSeventh\Session;
    use Slim\Container;

    /**
     * @param Container $container
     * @return Session
     */
    return function (Container $container) : Session {
        $session = new Session();
        /** @var Config $config */
        $config = $container[CONTAINER_CONFIG];
        $configSession = (array) $config->get('session', []);
        if (!empty($configSession)) {
            if (isset($configSession['save_path']) && $configSession['save_path']) {
                $session->getSession()->setSavePath($configSession['save_path']);
            }
            if (isset($configSession['name']) && $configSession['name']) {
                $session->getSession()->setName($configSession['name']);
            }
            if (isset($configSession['cache_limiter']) && $configSession['cache_limiter']) {
                $session->getSession()->setCacheLimiter($configSession['cache_limiter']);
            }
            if (isset($configSession['segment_name']) && $configSession['segment_name']) {
                $session->setSegmentName($configSession['segment_name']);
            }
            $cookieParamsKey = ['lifetime', 'path', 'domain', 'secure', 'httponly'];
            $cookieParams = [];
            foreach ($configSession as $key => $value) {
                in_array($key, $cookieParamsKey) && $cookieParams[$key] = $value;
            }

            $session->getSession()->setCookieParams($cookieParams);
        }

        $container[CONTAINER_LOG]->debug('Session initiated');
        return $session;
    };
}
