<?php
namespace PentagonalProject\ProjectSeventh;

/**
 * Class Arguments
 * @package PentagonalProject\ProjectSeventh
 */
class Arguments implements \Countable, \ArrayAccess
{
    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * Arguments constructor.
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->arguments =& $arguments;
    }

    /**
     * Get Arguments
     *
     * @param int|string|float $offset
     * @param null $default
     * @return mixed|null
     */
    public function &get($offset, $default = null)
    {
        if ($this->has($offset)) {
            return $this->arguments[$offset];
        }

        return $default;
    }

    /**
     * Check if has Arguments
     *
     * @param int|mixed|float $offset
     * @return bool
     */
    public function has($offset)
    {
        return (array_key_exists($offset, $this->arguments));
    }

    /**
     * Set Arguments
     *
     * @param int|string|float $offset
     * @param mixed $value
     */
    public function set($offset, $value)
    {
        $this->arguments[$offset] = $value;
    }

    /**
     * Remove/Unset Arguments
     *
     * @param int|string|float $offset
     */
    public function remove($offset)
    {
        unset($this->arguments[$offset]);
    }

    /**
     * Count Arguments
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
}
