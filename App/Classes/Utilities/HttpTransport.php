<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class HttpTransport
 * @package PentagonalProject\ProjectSeventh\Utilities
 *
 * @method ResponseInterface get(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface head(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface put(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface post(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface patch(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface delete(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface options(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface link(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface unLink(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface copy(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface purge(string|UriInterface $uri, array $options = [])
 *
 * @method PromiseInterface getAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface headAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface putAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface postAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface patchAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface deleteAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface optionsAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface linkAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface unLinkAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface copyAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface purgeAsync(string|UriInterface $uri, array $options = [])
 *
 */
class HttpTransport
{
    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * HttpTransport constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->guzzleClient = new Client($config);
    }

    /**
     * With Client
     *
     * @param Client $client
     * @return HttpTransport
     */
    public function withClient(Client $client) : HttpTransport
    {
        $clone = clone $this;
        $clone->guzzleClient = $client;
        return $clone;
    }

    /**
     * @param CookieJar|null $cookieJar
     * @return HttpTransport
     */
    public function withCookieJar(CookieJar $cookieJar = null)
    {
        $config = $this->guzzleClient->getConfig();
        $config['cookies'] = $cookieJar?: new CookieJar();

        return $this->withClient(new Client($config));
    }

    /**
     * Clone Without Cookies
     *
     * @return HttpTransport
     */
    public function withoutCookie()
    {
        $config = $this->guzzleClient->getConfig();
        unset($config['cookies']);

        return $this->withClient(new Client($config));
    }

    /**
     * @param array $config
     * @return HttpTransport
     */
    public static function create(array $config = []) : HttpTransport
    {
        return new static($config);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return PromiseInterface|ResponseInterface
     */
    public function __call(string $name, array $arguments)
    {
        // fix method
        $newName = strtolower(substr($name, -5));
        if (strtolower($newName) == 'async' && $newName !== 'Async') {
            $name = substr($name, 0, -5) . 'Async';
        }
        return $this->guzzleClient->__call(
            $name,
            $arguments
        );
    }
}
