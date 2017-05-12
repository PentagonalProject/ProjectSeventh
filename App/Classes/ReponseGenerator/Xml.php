<?php
namespace PentagonalProject\ProjectSeventh\ResponseGenerator;

use PentagonalProject\ProjectSeventh\Abstracts\ResponseGeneratorAbstract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * XML EXAMPLE FOR RETURNING GET REAL DATA VALUE
 * This for Tricky XML Return Data
 * if got invalid attribute will be use tag as
 * <tag key="key name of tag" type="type value"></tag>
 *
// SET DATA
[
    'Array1' => [               # array
        'ArrayNested2' => [     # array
            'Array With Invalid Tag Nested 2' => [ # array
                'No Array Key', # string
                'WithArrayKey' => [ # array
                    'Value 1',  # string
                    1,          # integer
                    true,       # boolean
                    false,      # boolean
                    null        # null
                ]
            ]
        ]
    ]
]

// XML RESPONSE
<?xml version="1.0" encoding="utf-8"?>
<root>
  <tag type="array" key="Array1">
    <tag type="array" key="ArrayNested2">
      <tag type="array" key="Array With Invalid Tag Nested 2">
        <integer key="0" type="string">No Array Key</integer>
        <tag type="array" key="WithArrayKey">
          <integer key="0" type="string">Value 1</integer>
          <integer key="1" type="integer">1</integer>
          <integer key="2" type="boolean">1</integer>
          <integer key="3" type="boolean">0</integer>
          <integer key="4" type="null"></integer>
        </tag>
      </tag>
    </tag>
  </tag>
</root>
 */
/**
 * Class Xml
 * @package PentagonalProject\ProjectSeventh\ResponseGenerator
 */
class Xml extends ResponseGeneratorAbstract
{
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
        $body = $this->getResponse()->getBody();
        $body->write($this->generateXML());
        $response = $this
            ->getResponse()
            ->withBody($body)
            ->withHeader('Content-Type', $this->getContentType());
        if ($this->getStatusCode()) {
            $response = $response->withStatus($this->getStatusCode());
        }

        return $response;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getKeyFor(string $type) : string
    {
        $return = $type;
        switch ($type) {
            case 'int':
                return 'integer';
            case 'bool':
                return 'boolean';
            case 'NULL':
            case 'null':
                return 'null';
        }

        return $return;
    }

    /**
     * Generate Pair XML
     *
     * @param mixed $content
     * @param int $counted
     * @return string
     */
    protected function generatePairXML($content, int $counted = 0)
    {
        $retVal = "";
        if (is_array($content)) {
            $counted_array = count($content);
            $c = 1;
            foreach ($content as $key => $value) {
                $count = $counted + 1;
                $tab = str_repeat("  ", $count);
                $type = gettype($key);
                $valueType = $this->getKeyFor(gettype($value));
                if (! is_string($key) || is_numeric($key)) {
                    $type   = is_numeric($key) ? 'integer' : $type;
                    $retVal .= "\n{$tab}<{$this->getKeyFor($type)} key=\"{$key}\" type=\"{$valueType}\">";
                    $retVal .= $this->generatePairXML($value, $count);
                    $endVal  = "</{$this->getKeyFor($type)}>";
                } elseif (preg_match('/[^a-z]/', $key[0]) !== false) {
                    // escape attribute
                    $key    = str_replace('"', '\"', $key);
                    $retVal .= "\n{$tab}<tag type=\"{$valueType}\" key=\"{$key}\">";
                    $retVal .= $this->generatePairXML($value, $count);
                    $endVal  = "</tag>";
                } else {
                    $retVal .= "\n{$tab}<{$key} type=\"{$valueType}\">";
                    $retVal .= $this->generatePairXML($value, $count);
                    $endVal = "</{$key}>";
                }
                if ($c === $counted_array) {
                    $endVal .= "\n";
                }
                $retVal .= (is_array($value) ? $tab : '') .$endVal;
                $c++;
            }
        } else {
            if (is_bool($content)) {
                return $content ? 1 : 0;
            }
            return $content;
        }

        return $retVal;
    }

    /**
     * Generate XML data
     *
     * @return string
     */
    protected function generateXML() : string
    {
        return "<?xml version=\"1.0\" encoding=\"{$this->getCharset()}\"?>"
        . "\n<root>"
        . $this->generatePairXML((array) $this->getData())
        . "</root>";
    }
}
