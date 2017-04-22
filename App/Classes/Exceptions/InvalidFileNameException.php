<?php
namespace PentagonalProject\ProjectSeventh\Exceptions;

use Apatis\Exceptions\Exception;

/**
 * Class InvalidFileName
 * @package PentagonalProject\ProjectSeventh\Exceptions
 */
class InvalidFileNameException extends Exception
{
    /**
     * @var string
     */
    protected $file;

    /**
     * InvalidFileNameException constructor.
     * @param string $file
     * @param string $message
     */
    public function __construct(string $file, string $message = '')
    {
        $this->file =  $file;
        if (func_num_args() > 1) {
            $this->message = $message;
        } else {
            $this->message = "Invalid file of {$this->file}";
        }
    }

    /**
     * @return string
     */
    public function getFileSet() : string
    {
        return $this->file;
    }
}
