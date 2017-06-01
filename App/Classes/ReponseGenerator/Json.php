<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use PentagonalProject\ProjectSeventh\Abstracts\ResponseGeneratorAbstract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

/**
 * Class Json
 * @package PentagonalProject\ProjectSeventh\ResponseGenerator
 */
class Json extends ResponseGeneratorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->setMimeType('application/json');
    }

    /**
     * {@inheritdoc}
     */
    public function serve(): ResponseInterface
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $response = $response->withJson(
            $this->getData(),
            $this->getStatusCode(),
            $this->getEncoding()
        );

        if ($response->hasHeader('Content-Length')) {
            $response = $response->withHeader(
                'Content-Length',
                $response->getBody()->getSize()
            );
        }

        return $response;
    }
}
