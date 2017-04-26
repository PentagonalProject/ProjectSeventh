<?php
namespace PentagonalProject\ProjectSeventh\Exceptions;

use Apatis\Exceptions\Exception;

/**
 * Class InvalidFileName
 * @package PentagonalProject\ProjectSeventh\Exceptions
 */
class InvalidPathException extends Exception
{
    /**
     * @var string
     */
    protected $path;

    /**
     * InvalidPathException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        $this->path = $path;
        if (func_num_args() > 1) {
            $this->message = $message;
        } else {
            $this->message = "Invalid path of {$path}";
        }
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }
}
