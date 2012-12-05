<?php

namespace BCC\EnumerableUtility;

use ArrayIterator;
use InvalidArgumentException;

class String implements IEnumerable
{
    use Enumerable;

    /**
     * @var string
     */
    protected $string;

    /**
     * @param string|string[] $string
     */
    function __construct($string = null)
    {
        if (is_array($string)) {
            $this->string = implode($string);
        }
        else if ($string instanceof String) {
            $this->string = $string->string;
        }
        else if (is_string($string)) {
            $this->string = $string;
        }
        else if ($string === null) {
            $this->string = '';
        }
        else {
            throw new InvalidArgumentException('You must give a string or a char array');
        }
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->string;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if ($this->string === '') {
            return new ArrayIterator();
        }

        $length = mb_strlen($this->string);
        $result = array();
        for($i = 0; $i < $length; $i++)
        {
            $result[] = mb_substr($this->string, $i, 1);
        }

        return new ArrayIterator($result);
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return strlen($this->string) > $offset;
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function offsetGet($offset)
    {
        return substr($this->string, $offset, 1);
    }

    /**
     * @param int $offset
     * @param string $value
     */
    public function offsetSet($offset, $value)
    {
        $before = substr($this->string, 0, $offset);
        $after  = substr($this->string, $offset + 1);

        $this->string = $before.$value.$after;
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        $before = substr($this->string, 0, $offset);
        $after  = substr($this->string, $offset + 1);

        $this->string = $before.$after;
    }

    /**
     * @param string $value
     * @param bool $ignoreCase
     *
     * @return bool
     */
    public function contains($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = mb_stripos($this->string, $value);
        }
        else {
            $pos = mb_strpos($this->string, $value);
        }

        return $pos !== false;
    }

    /**
     * @param string $value
     * @param bool $ignoreCase
     *
     * @return bool
     */
    public function endsWith($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = strripos($this->string, (string)$value);
        }
        else {
            $pos = strrpos($this->string, (string)$value);
        }

        return $pos === strlen($this->string) - strlen((string)$value);
    }

    /**
     * @param string $value
     * @param bool $ignoreCase
     *
     * @return bool
     */
    public function startsWith($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = stripos($this->string, (string)$value);
        }
        else {
            $pos = strpos($this->string, (string)$value);
        }

        return $pos === 0;
    }

    /**
     * @param string $value
     * @param bool $ignoreCase
     *
     * @return bool
     */
    public function equals($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $cmp = strcasecmp($this->string, (string)$value);
        }
        else {
            $cmp = strcmp($this->string, (string)$value);
        }

        return $cmp === 0;
    }

    /**
     * @param string $string
     * @param string[] $args
     *
     * @return String
     */
    public static function format($string, $args = null)
    {
        $args = array_slice(func_get_args(), 1);

        return new String(vsprintf((string)$string, $args));
    }

    /**
     * @param string $value
     * @param bool $ignoreCase
     *
     * @return int
     */
    public function indexOf($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = stripos($this->string, (string)$value);
        }
        else {
            $pos = strpos($this->string, (string)$value);
        }

        return $pos !== false ? $pos : -1;
    }

    /**
     * @param int $startIndex
     * @param string $value
     *
     * @return String
     */
    public function insert($startIndex, $value)
    {
        $before = substr($this->string, 0, $startIndex);
        $after  = substr($this->string, $startIndex);

        return new String($before.$value.$after);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isNullOrEmpty($string)
    {
        return strlen((string)$string) === 0;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isNullOrWhiteSpace($string)
    {
        return strlen(trim((string)$string)) === 0;
    }

    /**
     * @param string   $separator
     * @param string[] $strings
     *
     * @return String
     */
    public static function concatenate($separator, array $strings)
    {
        return new String(implode((string)$separator, $strings));
    }

    /**
     * @param string $value
     * @param bool   $ignoreCase
     *
     * @return int
     */
    public function lastIndexOf($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = strripos($this->string, (string)$value);
        }
        else {
            $pos = strrpos($this->string, (string)$value);
        }

        return $pos !== false ? $pos : -1;
    }

    /**
     * @param int    $totalWidth
     * @param string $paddingChar
     *
     * @return String
     */
    public function padLeft($totalWidth, $paddingChar = ' ')
    {
        return new String(str_pad($this->string, strlen($this->string)- mb_strlen($this->string) + $totalWidth, $paddingChar, STR_PAD_LEFT));
    }

    /**
     * @param int    $totalWidth
     * @param string $paddingChar
     *
     * @return String
     */
    public function padRight($totalWidth, $paddingChar = ' ')
    {
        return new String(str_pad($this->string, strlen($this->string)- mb_strlen($this->string) + $totalWidth, $paddingChar, STR_PAD_RIGHT));
    }

    /**
     * @param int $startIndex
     * @param int $count
     *
     * @return String
     */
    public function remove($startIndex, $count = null)
    {
        $first = mb_substr($this->string, 0, $startIndex);

        if ($count === null) {
            return new String($first);
        }

        $second = mb_substr($this->string, $startIndex + $count);

        return new String($first.$second);
    }

    /**
     * @param string $old
     * @param string $new
     * @param bool   $ignoreCase
     *
     * @return String
     */
    public function replace($old, $new, $ignoreCase = false)
    {
        if ($ignoreCase) {
            return new String(str_ireplace((string)$old, (string)$new, $this->string));
        }
        else {
            return new String(str_replace((string)$old, (string)$new, $this->string));
        }
    }

    /**
     * @param string $separator
     *
     * @return array
     */
    public function split($separator)
    {
        return explode((string)$separator, $this->string);
    }

    /**
     * @param int $startIndex
     * @param int $length
     *
     * @return String
     */
    public function subString($startIndex, $length = null)
    {
        if ($length === null) {
            return new String(mb_substr($this->string, (string)$startIndex));
        }

        return new String(mb_substr($this->string, (string)$startIndex, $length));
    }

    /**
     * @return String
     */
    public function toLower()
    {
        return new String(strtolower($this->string));
    }

    /**
     * @return String
     */
    public function toUpper()
    {
        return new String(strtoupper($this->string));
    }

    /**
     * @return string[]
     */
    public function toCharArray()
    {
        return $this->toArray();
    }

    /**
     * @param string $trimChars
     *
     * @return String
     */
    public function trim($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(trim($this->string));
        }

        return new String(trim($this->string, $trimChars));
    }

    /**
     * @param string $trimChars
     * @return String
     */
    public function trimEnd($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(rtrim($this->string));
        }

        return new String(rtrim($this->string, $trimChars));
    }

    /**
     * @param string $trimChars
     *
     * @return String
     */
    public function trimStart($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(ltrim($this->string));
        }

        return new String(ltrim($this->string, $trimChars));
    }
}
