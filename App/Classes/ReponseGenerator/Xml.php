<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use PentagonalProject\ProjectSeventh\Abstracts\ResponseGeneratorAbstract;
use PentagonalProject\ProjectSeventh\Utilities\XmlBuilderTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body;
use Slim\Http\Response;

/**
 * Class Xml
 * @package PentagonalProject\ProjectSeventh\ResponseGenerator
 */
class Xml extends ResponseGeneratorAbstract
{
    /**
     * @uses XmlBuilderTrait
     */
    use XmlBuilderTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->setMimeType('application/xml');
    }

    /**
     * {@inheritdoc}
     */
    public function serve(): ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write(
            $this->generateXML($this->getCharset(), (array) $this->getData())
        );
        $response = $this
            ->getResponse()
            ->withBody($body)
            ->withHeader('Content-Type', $this->getContentType());
        if ($this->getStatusCode()) {
            $response = $response->withStatus($this->getStatusCode());
        }
        /** @var Response $response */
        if ($response->hasHeader('Content-Length')) {
            $response = $response->withHeader(
                'Content-Length',
                $response->getBody()->getSize()
            );
        }

        return $response;
    }
}
