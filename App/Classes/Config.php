<?php
namespace PentagonalProject\ProjectSeventh;

use Apatis\ArrayStorage\CollectionFetch;
use Apatis\Exceptions\InvalidArgumentException;

/**
 * Class Config
 * @package PentagonalProject\ProjectSeventh
 */
class Config
{
    /**
     * @var CollectionFetch
     */
    protected $originalCollection;

    /**
     * @var CollectionFetch
     */
    protected $collection;

    /**
     * @var CollectionFetch
     */
    protected $lastCollection;
    /**
     * Config constructor.
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $this->collection = new CollectionFetch($setting);
        $this->originalCollection = clone $this->collection;
    }

    /**
     * Get Config
     *
     * @param string|null $key
     * @param mixed  $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (!func_num_args()) {
            return $this->collection->all();
        }

        return $this->collection->fetch($key, $default);
    }

    /**
     * Reset Collection to default
     */
    public function resetToDefault()
    {
        $this->lastCollection = $this->collection;
        $this->collection = clone $this->originalCollection;
    }

    /**
     * @return CollectionFetch
     */
    public function getCurrentCollection()
    {
        return $this->collection;
    }

    /**
     * @return CollectionFetch
     */
    public function getDefaultCollection()
    {
        return $this->originalCollection;
    }

    /**
     * @return CollectionFetch
     */
    public function getLastCollection()
    {
        return $this->lastCollection ?: $this->collection;
    }

    /**
     * @param array|string $key     string
     * @param mixed  $values  the value key name
     */
    public function set($key, $values = null)
    {
        if (is_array($key)) {
            $this->collection->replace($key);
            return;
        }

        if (!is_string($key) && !is_numeric($key)) {
            throw new InvalidArgumentException(
                'Invalid key name given! Key config must be as a string!'
            );
        }
        if (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $key, $matches)) > 1) {
            // Does the index contain array notation
            $the_key = null;
            $matches[0] = array_reverse($matches[0]);
            $old_key = null;
            for ($i = 0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');
                // Empty notation will return the value as array
                if ($key === '') {
                    $the_key[] = $the_key?: $values;
                    if (count($the_key) > 1) {
                        unset($the_key[key($the_key)]);
                    }
                    continue;
                }
                if (!isset($the_key)) {
                    $the_key[$key] = $values;
                    continue;
                }

                $the_key[$key] = $the_key;
                if (count($the_key) > 1) {
                    unset($the_key[key($the_key)]);
                }
            }

            $key = key($the_key);
            $values = is_array($this->collection[$key])
                ? array_merge($this->collection[$key], $the_key[$key])
                : $the_key;
            unset($the_key);
        }

        $this->collection->set($key, $values);
    }
}
