<?php
/* ------------------------------------------------------ *\
 |                     ROUTES EXAMPLE                     |
\* ------------------------------------------------------ */
/**
 * @uses \Slim\App
 * for instance of $this
 */
namespace {

    use PentagonalProject\ProjectSeventh\ResponseGenerator\Json;
    use PentagonalProject\ProjectSeventh\ResponseGenerator\Xml;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use Slim\App;
    use Slim\Http\Body;
    use Slim\Http\Response;

    if (!isset($this) || ! $this instanceof App) {
        header('HTTP/1.1 403 Forbidden');
        return;
    }

    $this->any(
        '/helo[/[{name: [A-Za-z0-9\S]+}[/]]]',
        function (
            ServerRequestInterface $request,
            ResponseInterface $response,
            $args
        ) {
            /**
             * @var Response $response
             * @var Body $body
             */
            $body = $response->getBody();
            $body->write(
                sprintf(
                    '"Helo <b>%1$s</b>, You are in (%2$s)',
                    isset($args['name']) ? $args['name'] : 'you!',
                    $request->getUri()
                )
            );
            return $response->withBody($body);
        }
    );

    $this->any(
        '/xml[/]',
        function (
            ServerRequestInterface $request,
            ResponseInterface $response
        ) {
            return Xml::generate($request, $response)
                ->setData([
                    'KeyData' => [
                        'TestDataArray' => 'Value',
                        'TestDataArray2' => 'Value',
                        'TestDataArrayValue' => [
                            'Value'
                        ],
                    ]
                ])
                ->setStatusCode(404)
                ->serve();
        }
    );

    $this->any(
        '/json[/]',
        function (
            ServerRequestInterface $request,
            ResponseInterface $response
        ) {
            return Json::generate($request, $response)
                ->setData([
                    'KeyData' => [
                        'TestDataArray' => 'Value',
                        'TestDataArray2' => 'Value',
                        'TestDataArrayValue' => [
                            'Value'
                        ],
                    ]
                ])
                // add encoding option
                ->setEncoding(JSON_PRETTY_PRINT | JSON_FORCE_OBJECT)
                ->setStatusCode(404)
                ->serve();
        }
    );
}
