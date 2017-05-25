<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use PentagonalProject\ProjectSeventh\Abstracts\ResponseGeneratorAbstract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
        $body = $this->getResponse()->getBody();
        $data = $this->getData();
        settype($data, 'string');
        $body->write($data);

        return $this
            ->getResponse()
            ->withBody($body)
            ->withStatus($this->getStatusCode())
            ->withHeader('Content-Type', $this->getContentType());
    }
}
