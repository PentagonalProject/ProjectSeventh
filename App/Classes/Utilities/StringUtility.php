<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

/**
 * Class StringUtility
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class StringUtility
{
    /**
     * Simple Extraction
     *
     * @param string $content
     * @return mixed
     */
    public static function extractUriFromString(string $content)
    {
        preg_match_all(
            '`
            (?:https?|ftps?|xmpp)\:\/\/
                [^\.]+\.[^\:\/]+
                (?:(?:\:[0-9]{2,8})?\/[^\s]+)?
            `sxmi',
            $content,
            $extracted
        );

        $extracted = current($extracted);
        if (is_array($extracted)) {
            $extracted = array_map(
                function ($ext) {
                    return trim($ext, ',.!?');
                },
                $extracted
            );
        }
        return $extracted;
    }

    /**
     * Check If Using PhPass from
     * {@link http://www.openwall.com/phpass}
     *
     * @param string $string has to check
     * @return bool
     */
    public static function isMaybeOpenWallPasswordHash($string)
    {
        if (!is_string($string)
            || ! in_array(($length = strlen($string)), [20, 34, 60])
            || preg_match('/[^a-zA-Z0-9\.\/\$\_]/', $string)
        ) {
            return false;
        }

        switch ((string) $length) {
            case '20':
                return !($string[0] != '_'
                    || strpos($string, '$') !== false
                    || strpos($string, '.') === false
                );
            case '34':
                return !(substr_count($string, '$') <> 2
                    || !in_array(substr($string, 0, 3), ['$P$', '$H$'])
                );
        }

        return !(
            substr($string, 0, 4) != '$2a$'
            || substr($string, 6, 1) != '$'
            || ! is_numeric(substr($string, 4, 2))
            || substr_count($string, '$') <> 3
        );
    }

    /**
     * Entities the Multi bytes deep string
     *
     * @param mixed $mixed  the string to detect multi bytes
     * @param bool  $entity true if want to entity the output
     *
     * @return mixed
     */
    public static function multiByteEntities($mixed, $entity = false)
    {
        static $hasIconV;
        static $limit;
        if (!isset($hasIconV)) {
            // safe resource check
            $hasIconV = function_exists('iconv');
        }

        if (!isset($limit)) {
            $limit = @ini_get('pcre.backtrack_limit');
            $limit = ! is_numeric($limit) ? 4096 : abs($limit);
            // minimum regex is 512 byte
            $limit = $limit < 512 ? 512 : $limit;
            // limit into 40 KB
            $limit = $limit > 40960 ? 40960 : $limit;
        }

        if (! $hasIconV && ! $entity) {
            return $mixed;
        }

        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::multiByteEntities($value, $entity);
            }
        } elseif (is_object($mixed)) {
            foreach (get_object_vars($mixed) as $key => $value) {
                $mixed->{$key} = self::multiByteEntities($value, $entity);
            }
        } /**
         * Work Safe with Parse @uses @var $limit Bit
         * | 4KB data split for regex callback & safe memory usage
         * that maybe fail on very long string
         */
        elseif (strlen($mixed) > $limit) {
            return implode('', self::multiByteEntities(str_split($mixed, $limit), $entity));
        }

        if ($entity) {
            $mixed = htmlentities(html_entity_decode($mixed));
        }

        return $hasIconV
            ? preg_replace_callback(
                '/[\x{80}-\x{10FFFF}]/u',
                function ($match) {
                    $char = current($match);
                    $utf = iconv('UTF-8', 'UCS-4//IGNORE', $char);
                    return sprintf("&#x%s;", ltrim(strtolower(bin2hex($utf)), "0"));
                },
                $mixed
            ) : $mixed;
    }

    /* --------------------------------------------------------------------------------*
     |                              Serialize Helper                                   |
     |                                                                                 |
     | Custom From WordPress Core wp-includes/functions.php                            |
     |---------------------------------------------------------------------------------|
     */

    /**
     * Check value to find if it was serialized.
     * If $data is not an string, then returned value will always be false.
     * Serialized data is always a string.
     *
     * @param  mixed $data   Value to check to see if was serialized.
     * @param  bool  $strict Optional. Whether to be strict about the end of the string. Defaults true.
     * @return bool  false if not serialized and true if it was.
     */
    public static function isSerialized($data, $strict = true)
    {
        /* if it isn't a string, it isn't serialized
         ------------------------------------------- */
        if (! is_string($data) || trim($data) == '') {
            return false;
        }

        $data = trim($data);
        // null && boolean
        if ('N;' == $data || $data == 'b:0;' || 'b:1;' == $data) {
            return true;
        }

        if (strlen($data) < 4 || ':' !== $data[1]) {
            return false;
        }

        if ($strict) {
            $last_char = substr($data, -1);
            if (';' !== $last_char && '}' !== $last_char) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');

            // Either ; or } must exist.
            if (false === $semicolon && false === $brace
                || false !== $semicolon && $semicolon < 3
                || false !== $brace && $brace < 4
            ) {
                return false;
            }
        }

        $token = $data[0];
        switch ($token) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            // or else fall through
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }

        return false;
    }

    /**
     * Un-serialize value only if it was serialized.
     *
     * @param  string $original Maybe un-serialized original, if is needed.
     * @return mixed  Un-serialized data can be any type.
     */
    public static function maybeUnSerialize($original)
    {
        if (! is_string($original) || trim($original) == '') {
            return $original;
        }

        /**
         * Check if serialized
         * check with trim
         */
        if (self::isSerialized($original)) {
            /**
             * use trim if possible
             * Serialized value could not start & end with white space
             */
            return @unserialize(trim($original));
        }

        return $original;
    }

    /**
     * Serialize data, if needed. @uses for ( un-compress serialize values )
     * This method to use safe as save data on database. Value that has been
     * Serialized will be double serialize to make sure data is stored as original
     *
     *
     * @param  mixed $data Data that might be serialized.
     * @return mixed A scalar data
     */
    public static function maybeSerialize($data)
    {
        if (is_array($data) || is_object($data)) {
            return @serialize($data);
        }

        // Double serialization is required for backward compatibility.
        if (self::isSerialized($data, false)) {
            return serialize($data);
        }

        return $data;
    }
}
