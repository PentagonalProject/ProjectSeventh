<?php
namespace PentagonalProject\ProjectSeventh\Abstracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseGeneratorAbstract
 * @package PentagonalProject\ProjectSeventh\Abstracts
 */
abstract class ResponseGeneratorAbstract
{
    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var bool
     */
    protected $recheckMimeType = false;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var int
     */
    protected $encoding = 0;

    /**
     * ResponseGeneratorAbstract constructor.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->setRequest($request);
        $this->setResponse($response);
        $this->setStatusCode($response->getStatusCode());
    }

    /**
     * Set Data
     *
     * @param mixed $data
     * @return static
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getEncoding(): int
    {
        return $this->encoding;
    }

    /**
     * @param int $encoding
     * @return static
     */
    public function setEncoding(int $encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Set Character Set
     *
     * @param string $data
     * @return static
     */
    public function setCharset($data)
    {
        if (is_string($data)) {
            $data = strtolower(trim($data));
            if ($data == '') {
                $data = null;
            } elseif (preg_match('/([^0-9]*)[\-]([0-9]+)?$/', $data, $match)) {
                // sanitize to default utf8
                $data = $match[0] . '-' . (!empty($match[1]) ? $match[1] : '8');
            }
        }

        $this->charset = $data;
        return $this;
    }

    /**
     * Get character set
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Maps a file extensions to a mimeType.
     *
     * @param $extension string The file extension.
     *
     * @return string|null
     * @link http://svn.apache.org/repos/asf/httpd/httpd/branches/1.3.x/conf/mime.types
     */
    public static function getMimeTypeFromExtension(string $extension)
    {
        static $mimeTypes = [
            '7z' => 'application/x-7z-compressed',
            'aac' => 'audio/x-aac',
            'ai' => 'application/postscript',
            'aif' => 'audio/x-aiff',
            'asc' => 'text/plain',
            'asf' => 'video/x-ms-asf',
            'atom' => 'application/atom+xml',
            'avi' => 'video/x-msvideo',
            'bmp' => 'image/bmp',
            'bz2' => 'application/x-bzip2',
            'cer' => 'application/pkix-cert',
            'crl' => 'application/pkix-crl',
            'crt' => 'application/x-x509-ca-cert',
            'css' => 'text/css',
            'csv' => 'text/csv',
            'cu' => 'application/cu-seeme',
            'deb' => 'application/x-debian-package',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dvi' => 'application/x-dvi',
            'eot' => 'application/vnd.ms-fontobject',
            'eps' => 'application/postscript',
            'epub' => 'application/epub+zip',
            'etx' => 'text/x-setext',
            'flac' => 'audio/flac',
            'flv' => 'video/x-flv',
            'gif' => 'image/gif',
            'gz' => 'application/gzip',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ico' => 'image/x-icon',
            'ics' => 'text/calendar',
            'ini' => 'text/plain',
            'iso' => 'application/x-iso9660-image',
            'jar' => 'application/java-archive',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'js' => 'text/javascript',
            'json' => 'application/json',
            'latex' => 'application/x-latex',
            'log' => 'text/plain',
            'm4a' => 'audio/mp4',
            'm4v' => 'video/mp4',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            'mp4a' => 'audio/mp4',
            'mp4v' => 'video/mp4',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpg4' => 'video/mp4',
            'oga' => 'audio/ogg',
            'ogg' => 'audio/ogg',
            'ogv' => 'video/ogg',
            'ogx' => 'application/ogg',
            'pbm' => 'image/x-portable-bitmap',
            'pdf' => 'application/pdf',
            'pgm' => 'image/x-portable-graymap',
            'png' => 'image/png',
            'pnm' => 'image/x-portable-anymap',
            'ppm' => 'image/x-portable-pixmap',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ps' => 'application/postscript',
            'qt' => 'video/quicktime',
            'rar' => 'application/x-rar-compressed',
            'ras' => 'image/x-cmu-raster',
            'rss' => 'application/rss+xml',
            'rtf' => 'application/rtf',
            'sgm' => 'text/sgml',
            'sgml' => 'text/sgml',
            'svg' => 'image/svg+xml',
            'swf' => 'application/x-shockwave-flash',
            'tar' => 'application/x-tar',
            'tiff' => 'image/tiff',
            'torrent' => 'application/x-bittorrent',
            'ttf' => 'application/x-font-ttf',
            'txt' => 'text/plain',
            'wav' => 'audio/x-wav',
            'webm' => 'video/webm',
            'wma' => 'audio/x-ms-wma',
            'wmv' => 'video/x-ms-wmv',
            'woff' => 'application/x-font-woff',
            'wsdl' => 'application/wsdl+xml',
            'xbm' => 'image/x-xbitmap',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xml' => 'application/xml',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-xwindowdump',
            'yaml' => 'text/yaml',
            'yml' => 'text/yaml',
            'zip' => 'application/zip',
        ];

        $extension = strtolower($extension);

        return isset($mimeTypes[$extension])
            ? $mimeTypes[$extension]
            : null;
    }

    /**
     * Generate content type
     *
     * @uses $this->getMimeTypeFromExtension()
     * @return string
     */
    protected function fixMimeType()
    {
        $this->mimeType = !is_string($this->mimeType) || trim($this->mimeType) == ''
            ? 'text/html'
            : strtolower(trim($this->mimeType));

        if ($this->recheckMimeType || strpos($this->mimeType, '/') === false) {
            $selectedContentTypes = array_filter(explode(',', $this->mimeType));
            if (count($selectedContentTypes)) {
                $this->mimeType = current($selectedContentTypes);
            }
            if (preg_match(
                '/(?:(?:[^/]*)(?:\\\+|\/+))?(html?|javascript|calendar|css|plain)/',
                $this->mimeType,
                $match
            ) && !empty($match[1])
            ) {
                $this->mimeType = $this->mimeType == 'htm'
                    ? 'html'
                    : $this->mimeType;
                $this->mimeType = "text/{$match[1]}";
            } elseif (strpos($this->mimeType, 'ico') !== false || strpos($this->mimeType, 'icns') !== false) {
                $this->mimeType = 'image/x-icon';
                $charset = null;
            } elseif (strpos($this->mimeType, 'sgm') !== false) {
                $this->mimeType = 'text/sgml';
            } elseif (preg_match(
                '/(?:(?:[^/]*)(?:\\\+|\/+))?(ja?son|xml|ogg|pdf|postscript|zip|ttf2?)/',
                $this->mimeType,
                $match
            ) && !empty($match[1])
            ) {
                if ($match[1] == 'jason') {
                    $match[1] = 'json';
                } elseif ($match[1] == 'ttf2' || $match[1] == 'ttf') {
                    $match[1] = 'x-font-ttf';
                }
                $this->mimeType = 'application/' . $match[1];
            } elseif (preg_match(
                '/(?:(?:[^/]*)(?:\\\+|\/+))?(jpe?g?|png|w?bmp|gif|pbm|tif(?:f+)?|png|ppm|ras|xbm|xpm|xwd)/',
                $this->mimeType,
                $match
            )
                && !empty($match[1])
            ) {
                if ($match[1] == 'wbmp') {
                    $match[1] = 'bmp';
                } elseif (strpos($match[1], 'tif') !== false) {
                    $match[1] = 'tiff';
                }
                $this->mimeType = $this->getMimeTypeFromExtension($match[1]);
            } else {
                $mimeType = null;
                $this->mimeType = preg_replace('/(\\\|\/)+/', '/', trim($this->mimeType));
                if (preg_match('/([^\]*)(?:\/(.+(\+.+)?)/?', $this->mimeType, $match) && !empty($match)) {
                    if (!empty($match[3])) {
                        $mimeType = $this->getMimeTypeFromExtension($match[3]);
                    }
                    if (!$mimeType && !empty($match[2])) {
                        $mimeType = $this->getMimeTypeFromExtension($match[2]);
                    }
                    if (!$mimeType) {
                        $mimeType = $this->getMimeTypeFromExtension($match[1]);
                    }
                }
                if (!$mimeType) {
                    if (preg_match('/te?xt|plain|ini/', $this->mimeType)) {
                        $mimeType = 'txt';
                    }
                    $mimeType = $this->getMimeTypeFromExtension($mimeType);
                    // fallback to default `text/html`
                    if (!$mimeType) {
                        $mimeType = 'text/html';
                    }
                    $this->mimeType = $mimeType;
                }
            }
        }

        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getContentType() : string
    {
        $charset = $this->getCharset();
        return $this->getMimeType() . ($charset ? ';charset=' . $charset : '');
    }

    /**
     * Get Mime Type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->fixMimeType();
    }

    /**
     * Set Mime Type
     *
     * @param string $mimeType
     * @return static
     */
    public function setMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Set Response Status
     *
     * @param int $status
     * @return static
     */
    public function setStatusCode($status)
    {
        if ($this->response->withStatus($status)->getReasonPhrase() == '') {
            throw new \InvalidArgumentException(
                'Invalid response code given.',
                E_USER_ERROR
            );
        }

        $this->statusCode = abs($status);
        return $this;
    }

    /**
     * Get Status Code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Generate
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return static
     */
    public static function generate(RequestInterface $request, ResponseInterface $response)
    {
        return new static($request, $response);
    }

    /**
     * Serve The response
     *
     * @return ResponseInterface
     */
    abstract public function serve() : ResponseInterface;

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Set Override Request
     *
     * @param RequestInterface $request
     * @return static
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set Override Response
     *
     * @param ResponseInterface $response
     * @return static
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }
}
