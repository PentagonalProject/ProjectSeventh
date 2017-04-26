<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

use Apatis\Exceptions\Exception;
use Apatis\Exceptions\InvalidArgumentException;
use Apatis\Exceptions\RuntimeException;
use PentagonalProject\ProjectSeventh\Abstracts\EmbeddedSystem;
use PentagonalProject\ProjectSeventh\Exceptions\EmptyFileException;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidPathException;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidEmbeddedException;

/**
 * Class EmbeddedReader
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class EmbeddedReader
{
    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var string|bool
     */
    protected $file = false;

    /**
     * @var EmbeddedSystem
     */
    protected $instance;

    /**
     * @var string
     */
    protected $embeddedClass = EmbeddedSystem::class;

    /**
     * @var string
     */
    protected $name = 'Embed';

    /**
     * EmbeddedReader constructor.
     */
    final public function __construct()
    {
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set File
     *
     * @param string $file
     * @return EmbeddedReader
     */
    protected function setFileToLoad(string $file) : EmbeddedReader
    {
        if (file_exists($file)) {
            $spl = new \SplFileInfo($file);
            if ($spl->isLink()) {
                throw new InvalidArgumentException(
                    "Argument could not as a symlink.",
                    E_WARNING
                );
            }
            if (!$spl->isFile()) {
                throw new InvalidArgumentException(
                    "Argument is not a file.",
                    E_WARNING
                );
            }

            if (strtolower($spl->getExtension()) !== 'php') {
                throw new InvalidArgumentException(
                    sprintf(
                        "%s file has invalid extension. Extension must be as `php`",
                        $this->getName()
                    ),
                    E_WARNING
                );
            }

            $this->file = $spl->getRealPath();
            unset($spl);
            return $this;
        }

        throw new InvalidArgumentException(
            sprintf(
                "Invalid file %s to read.",
                $this->getName()
            ),
            E_WARNING
        );
    }

    /**
     * Create Instance EmbeddedReader
     *
     * @param string $file
     * @return static
     */
    public static function create(string $file)
    {
        $static = new static();
        return $static->setFileToLoad($file);
    }

    /**
     * Get File Path
     *
     * @return bool|string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get Directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return dirname($this->file);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return EmbeddedSystem|null
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return EmbeddedReader
     * @throws InvalidPathException
     * @throws Exception
     */
    public function process() : EmbeddedReader
    {
        if (!$this->getFile()) {
            $this->valid = false;
        }

        // stop
        if (isset($this->valid)) {
            return $this;
        }

        if (preg_match('/[^a-z0-9\_]/i', pathinfo($this->file, PATHINFO_FILENAME))) {
            throw new InvalidPathException(
                $this->file,
                sprintf(
                    "Invalid base file name for %s, file name must be contain alpha numeric and underscore only",
                    basename($this->file)
                )
            );
        }

        return $this->validate();
    }

    /**
     * Validate
     *
     * @return EmbeddedReader
     * @throws EmptyFileException
     * @throws InvalidEmbeddedException
     */
    private function validate() : EmbeddedReader
    {
        if (!is_string($this->embeddedClass)) {
            throw new RuntimeException(
                sprintf(
                    'Invalid Parent %s Class. %s extends must be as class name and string.',
                    $this->getName()
                ),
                E_COMPILE_ERROR
            );
        }

        $this->embeddedClass = rtrim($this->embeddedClass, '\\');
        if (!class_exists($this->embeddedClass)
            || strtolower($this->embeddedClass) != strtolower(EmbeddedSystem::class)
                && ! is_subclass_of($this->embeddedClass, EmbeddedSystem::class)
        ) {
            throw new RuntimeException(
                sprintf(
                    'Parent %1$s class does not extends into %2$s',
                    $this->getName(),
                    EmbeddedSystem::class
                )
            );
        }

        /**
         * strip white space is remove all new line and double spaces
         * and remove all comments
         * @see php_strip_whitespace()
         * just het 204b byte to get content
         */
        $content = substr(php_strip_whitespace($this->getFile()), 0, 2048);
        if (!$content) {
            throw new EmptyFileException(
                $this->getFile()
            );
        }

        if (strtolower(substr($content, 0, 5)) !== '<?php') {
            throw new InvalidEmbeddedException(
                sprintf(
                    'Invalid %s, %s does not start with open php tag.',
                    $this->getName()
                ),
                E_ERROR
            );
        }

        $namespace = '\\';
        if (preg_match('/\<\?php\s+namespace\s+(?P<namespace>[^;\{]+)/ms', $content, $namespaces)
            && !empty($namespaces['namespace'])
        ) {
            if (strtolower(trim($namespaces['namespace'])) == strtolower(__NAMESPACE__)) {
                throw new InvalidEmbeddedException(
                    sprintf(
                        'File %s contain name space of core.',
                        $this->getName()
                    ),
                    E_ERROR
                );
            }
            $namespace .= $namespaces['namespace'];
        }

        if ($namespace !== '\\' && preg_match('`[^\\_a-z0-9]`', $namespace)) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'File %s contain invalid name space.',
                    $this->getName()
                ),
                E_ERROR
            );
        }

        $embeddedClass = $this->embeddedClass;
        preg_match(
            '/use\s+
                (?:\\\{1})?(?P<extended>'.preg_quote($embeddedClass, '/').')
                (?:\s+as\s+(?P<alias>[a-z0-9_]+))?;+
            /smx',
            $content,
            $asAlias
        );

        $alias = isset($asAlias['alias'])
            ? $asAlias['alias']
            : null;
        if (!$alias && isset($asAlias['extended'])) {
            $asAlias['extended'] = explode('\\', $asAlias['extended']);
            $alias = end($asAlias['extended']);
        }

        // remove declarations
        if (stripos($content, 'declare') !== false) {
            $content = preg_replace('`declare\s*\([^\)]+\)\s*\;\s*`smi', '', $content);
        }

        // replace for unused text
        $content = preg_replace(
            [
                '`^\<\?php\s+(?:namespace\s+([^;\{])*[;\{]\s*)?`smi',
                '`(use[^;]+;\s*)*\s*(class)`smi'
            ],
            '$2',
            $content
        );

        $regexNameSpace = $alias
            ? '(?P<extends>('.preg_quote("{$alias}", '/').'))\s*'
            : '(?P<extends>('.preg_quote("\\{$embeddedClass}", '/') . '|' . preg_quote($alias, '/').'))\s*';
        preg_match(
            '`class\s+
                    (?P<class>[a-z_][a-z0-9\_]+)
                    \s+extends\s+
                    '.$regexNameSpace.'
            `msix',
            $content,
            $class
        );

        if (empty($class['class']) || empty($class['extends'])) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'File %1$s does not contain valid class or not extends to `%2$s`.',
                    $this->getName(),
                    $embeddedClass
                ),
                E_ERROR
            );
        }

        if (strtolower(pathinfo($this->file, PATHINFO_FILENAME)) !== strtolower($class['class'])) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'File %s does not match between file name & class.',
                    $this->getName()
                ),
                E_ERROR
            );
        }

        if (! preg_match('/(public\s+)?function\s+init\([^\)]*\)\s*\{/smi', $content, $match)) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'File %s does not contain method `init`.',
                    $this->getName()
                ),
                E_ERROR
            );
        }

        $class = $class['class'];
        $namespace = rtrim($namespace, '\\');
        $class = "{$namespace}\\{$class}";

        // prevent multiple include file if class has been loaded
        if (class_exists($class)) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'Object class %1$s for %2$s has been loaded.',
                    $class,
                    $this->getName()
                ),
                E_ERROR
            );
        }

        // start buffer
        ob_start();

        // include once
        IncludeFileOnce($this->file);
        if ($error = error_get_last() && !empty($error) && $error['file'] == $this->file) {
            if ($error['type'] === E_ERROR) {
                @ob_end_clean();
                throw new InvalidEmbeddedException(
                    sprintf(
                        'File %s contains fatal error.',
                        $this->getName()
                    ),
                    E_ERROR
                );
            }
        }
        @ob_end_clean();
        if (!class_exists($class)) {
            throw new InvalidEmbeddedException(
                sprintf(
                    'File %1$s does not contain class %2$s.',
                    $this->getName(),
                    $class
                ),
                E_ERROR
            );
        }

        $this->valid = true;
        $this->instance = new $class;
        return $this;
    }
}
