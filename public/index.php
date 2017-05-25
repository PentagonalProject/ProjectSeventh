<?php
/* ------------------------------------------------------ *\
 |                      INDEX FILE                        |
\* ------------------------------------------------------ */

/**
 * Minimum PHP Version is 7.0
 */
if (version_compare(phpversion(), "7.0", "<")) {
    preg_match('`^\d+(\.\d+)*`', PHP_VERSION, $match);
    $content = sprintf("Minimum Requirement php version is 7.0 and %s given", $match[0]);
    if (php_sapi_name() !== 'cli') {
        $accept = isset($_SERVER['CONTENT_TYPE'])
            ? [$_SERVER['CONTENT_TYPE']]
            : ( isset($_SERVER['HTTP_ACCEPT'])
                ? explode(',', $_SERVER['HTTP_ACCEPT'])
                : ["text/html"]
            );
        $accept = strtolower($accept[0]);
        $current = null;
        if (preg_match('/\/(?P<content>ja?son|javascript|xml|plain)$/', trim($accept), $match)
            && !empty($match['content'])
        ) {
            $current = $match['content'];
        }
        switch ($current) {
            case 'json':
            case 'jason':
                $accept = "application/json";
                $content = json_encode(
                    [
                        "error" => [
                            "code"    => 500,
                            "message" => [$content]
                        ]
                    ],
                    JSON_PRETTY_PRINT
                );
                break;
            case 'xml':
                $accept = "application/xml";
                $content = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <error type="array">
        <code type="int">500</code>
        <message type="array" element="integer">
            <integer key="0" type="string">{$content}</integer>
        </message>
    </error>
</root>
EOF;

                break;
            case 'plain':
                $accept = "text/plain";
                break;
            default:
                $content = <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Internal Server Error</title>
  <style type="text/css">
    body {
        font-size: 14px;
        font-family: "Helvetica", arial, sans-serif;
        font-weight: normal;
        line-height: 1;
        vertical-align: baseline;
        padding:0;
        margin: 0;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    h2 {
        margin: 0 0 1em 0;
        font-size: 1.5em;
        padding: 20px 10px;
        background-color: #dc493c;
        color: #fff;
        font-weight: lighter;
        letter-spacing: 1px;
    }
  </style>
</head>
<body>
  <h2 class="warning">{$content}</h2>
</body>
</html>
EOF;
        }

        header("Content-Type: {$accept}", true, 200);
        $length = strlen($content);
        header("Content-Length: {$length}", true, 200);
    } else {
        $content = "\n(Error) {$content}\n\n";
    }

    echo $content;
    exit(E_CORE_ERROR);
}

// Require Composer Autoload
require __DIR__ . '/../vendor/autoload.php';
// if the composer that must not ../App/FunctionIncludes.php
// try to includes once (beware FunctionsIncludes.php must be load after vendor loaded)
require_once __DIR__ .'/../App/FunctionsIncludes.php';
return (new PentagonalProject\ProjectSeventh\Application())
    ->process((array) require __DIR__ . '/../Config/Config.php');
