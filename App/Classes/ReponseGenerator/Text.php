<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Text
 * @package PentagonalProject\ProjectSeventh\ResponseGenerator
 */
class Text extends Html
{
    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response);
        $this->setMimeType('text/plain');
    }
}
