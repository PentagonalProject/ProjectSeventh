<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\Exceptions\Exception;
use Apatis\Exceptions\InvalidArgumentException;
use PentagonalProject\ProjectSeventh\Exceptions\EmptyFileException;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidFileNameException;
use PentagonalProject\ProjectSeventh\Exceptions\InvalidModuleException;

/**
 * Class ModuleReader
 * @package PentagonalProject\ProjectSeventh
 */
class ModuleReader
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
     * @var Module
     */
    protected $instance;

    /**
     * ModuleReader constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        file_exists($file) && ($this->file = stream_resolve_include_path($file));
        if ($this->file) {
            clearstatcache($this->file);
            if (pathinfo($this->file, PATHINFO_EXTENSION) !== 'php') {
                $this->file = false;
                throw new InvalidArgumentException(
                    "Module file has invalid extension. Extension must be as `php`",
                    E_WARNING
                );
            }

            return;
        }

        throw new InvalidArgumentException(
            "Invalid file Module to read",
            E_WARNING
        );
    }

    /**
     * Get File Path
     *
     * @return bool|string
     */
    public function getFilePath()
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return Module|null
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return ModuleReader
     * @throws InvalidFileNameException
     * @throws Exception
     */
    public function process() : ModuleReader
    {
        if (!$this->getFilePath()) {
            $this->valid = false;
        }

        // stop
        if (isset($this->valid)) {
            return $this;
        }

        if (preg_match('/[^a-z0-9\_]/i', basename($this->file))) {
            throw new InvalidFileNameException(
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
     * @return ModuleReader
     * @throws EmptyFileException
     * @throws InvalidModuleException
     */
    private function validate() : ModuleReader
    {
        /**
         * strip white space is remove all new line and double spaces
         * and remove all comments
         * @see php_strip_whitespace()
         * just het 204b byte to get content
         */
        $content = substr(php_strip_whitespace($this->getFilePath()), 0, 2048);
        if (!$content) {
            throw new EmptyFileException(
                $this->getFilePath()
            );
        }
        if (strtolower(substr($content, 0, 4)) !== '<?php') {
            throw new InvalidModuleException(
                "Invalid module, module does not start with open php tag.",
                E_ERROR
            );
        }

        $namespace = '\\';
        if (preg_match('/\<\?php\s+namespace\s+(?p<namespace>[^;]+)/ms', $content, $namespaces)
            && !empty($namespaces['namespace'])
        ) {
            if (strtolower(trim($namespaces['namespace'])) == strtolower(__NAMESPACE__)) {
                throw new InvalidModuleException(
                    "File Module contain name space of core.",
                    E_ERROR
                );
            }
            $namespace .= $namespaces['namespace'];
        }

        if ($namespace !== '\\' && preg_match('`[^\\_a-z0-9]`', $namespace)) {
            throw new InvalidModuleException(
                "File Module contain invalid name space.",
                E_ERROR
            );
        }

        $moduleClass = Module::class;
        preg_match(
            '/use\s+
                (?:\\\{1})?'.preg_quote($moduleClass, '/').'
                (?:\s+as\s+(?P<alias>[a-z0-9_]+))?;+
            /smx',
            $content,
            $asAlias
        );

        $alias = isset($asAlias['alias']) ? $asAlias['alias'] : null;
        // replace for unused text
        $content = preg_replace(
            [
                '`^\<\?php\s+(?:namespace\s+[^;]+;\s*)?`smi',
                '`(?:use[^;]+;\s*)*\s*(class)`smi'
            ],
            '$1',
            $content
        );

        $regexNameSpace = $alias
            ? '(?P<extends>('.preg_quote("\\{$moduleClass}", '/').'))\s*'
            : '(?P<extends>('.preg_quote("\\{$moduleClass}", '/') . '|' . preg_quote($alias, '/').'))\s*';
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
            throw new InvalidModuleException(
                "File Module does not contain valid class or not extends to `{$moduleClass}`.",
                E_ERROR
            );
        }

        if (strtolower(pathinfo(basename($this->file), PATHINFO_FILENAME)) !== strtolower($class['class'])) {
            throw new InvalidModuleException(
                "File Module does not match between file name & class.",
                E_ERROR
            );
        }

        if (! preg_match('/(public\s+)?function\s+init\([^\)]*\)\s*\{/smi', $content, $match)) {
            throw new InvalidModuleException(
                "File Module does not contain method `init`.",
                E_ERROR
            );
        }

        $namespace = rtrim($namespace, '\\');
        $class = "{$namespace}\\{$class}";
        // start buffer
        ob_start();
        // include once
        IncludeFileOnce($this->file);
        if ($error = error_get_last() && !empty($error) && $error['file'] == $this->file) {
            if ($error['type'] === E_ERROR) {
                @ob_end_clean();
                throw new InvalidModuleException(
                    "File Module contains fatal error.",
                    E_ERROR
                );
            }
        }
        @ob_end_clean();
        if (!class_exists($class)) {
            throw new InvalidModuleException(
                "File Module does not contain class {$class}.",
                E_ERROR
            );
        }

        $this->instance = new $class;
        return $this;
    }
}
