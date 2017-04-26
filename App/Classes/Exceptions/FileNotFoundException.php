<?php
namespace PentagonalProject\ProjectSeventh\Exceptions;

/**
 * Class FileNotFoundException
 * @package PentagonalProject\ProjectSeventh\Exceptions
 */
class FileNotFoundException extends InvalidPathException
{
    /**
     * FileNotFoundException constructor.
     * @param string $path
     * @param string $message
     */
    public function __construct(string $path, string $message = '')
    {
        $message = $message ?: "File {$path} has not found!";
        parent::__construct($path, $message);
    }
}
