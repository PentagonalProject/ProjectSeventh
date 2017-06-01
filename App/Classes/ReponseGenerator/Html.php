<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use PentagonalProject\ProjectSeventh\Abstracts\ResponseGeneratorAbstract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body;

/**
 * Class Html
 * @package PentagonalProject\ProjectSeventh\ResponseGenerator
 */
class Html extends ResponseGeneratorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->setMimeType('text/html');
    }

    /**
     * {@inheritdoc}
     */
    public function serve(): ResponseInterface
    {
        $body = new Body(fopen('php://temp', 'r+'));
        $data = $this->getData();
        settype($data, 'string');
        $body->write($data);

        $response = $this
            ->getResponse()
            ->withBody($body)
            ->withStatus($this->getStatusCode())
            ->withHeader('Content-Type', $this->getContentType());

        if ($response->hasHeader('Content-Length')) {
            $response = $response->withHeader(
                'Content-Length',
                $response->getBody()->getSize()
            );
        }

        return $response;
    }
}
