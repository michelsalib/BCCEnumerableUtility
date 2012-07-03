<?php

namespace BCC\EnumerableUtility;

class String implements IEnumerable
{
    use Enumerable;

    /**
     * @var string
     */
    protected $string;

    function __construct($string = null)
    {
        if (is_array($string)) {
            $this->string = String::concatenate('', $string)->__toString();
        }
        /** @var $string String */
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
            throw new \InvalidArgumentException('You must give a string or a char array');
        }
    }

    function __toString()
    {
        return $this->string;
    }

    public function getIterator()
    {
        if ($this->string === '') {
            return array();
        }

        return \str_split($this->string);
    }

    public function offsetExists($offset)
    {
        return \strlen($this->string) > $offset;
    }

    public function offsetGet($offset)
    {
        return \substr($this->string, $offset, 1);
    }

    public function offsetSet($offset, $value)
    {
        $before = \substr($this->string, 0, $offset);
        $after  = \substr($this->string, $offset + 1);

        $this->string = $before.$value.$after;
    }

    public function offsetUnset($offset)
    {
        $before = \substr($this->string, 0, $offset);
        $after  = \substr($this->string, $offset + 1);

        $this->string = $before.$after;
    }

    public function contains($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = \stripos($this->string, $value);
        }
        else {
            $pos = \strpos($this->string, $value);
        }

        return $pos !== false;
    }

    public function endsWith($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = \strripos($this->string, $value);
        }
        else {
            $pos = \strrpos($this->string, $value);
        }

        return $pos === \strlen($this->string) - \strlen($value);
    }

    public function startsWith($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = \stripos($this->string, $value);
        }
        else {
            $pos = \strpos($this->string, $value);
        }

        return $pos === 0;
    }

    public function equals($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $cmp = \strcasecmp($this->string, $value);
        }
        else {
            $cmp = \strcmp($this->string, $value);
        }

        return $cmp === 0;
    }

    public static function format($string, $args = null)
    {
        $args = \array_slice(\func_get_args(), 1);

        return new String(\vsprintf($string, $args));
    }

    public function indexOf($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = \stripos($this->string, $value);
        }
        else {
            $pos = \strpos($this->string, $value);
        }

        return $pos !== false ? $pos : -1;
    }

    public function insert($startIndex, $value)
    {
        $before = \substr($this->string, 0, $startIndex);
        $after  = \substr($this->string, $startIndex);

        return new String($before.$value.$after);
    }

    public static function isNullOrEmpty($string)
    {
        return \strlen($string) === 0;
    }

    public static function isNullOrWhiteSpace($string)
    {
        return \strlen(\trim($string)) === 0;
    }

    public static function concatenate($separator, array $strings)
    {
        return new String(\implode($separator, $strings));
    }

    public function lastIndexOf($value, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $pos = \strripos($this->string, $value);
        }
        else {
            $pos = \strrpos($this->string, $value);
        }

        return $pos !== false ? $pos : -1;
    }

    public function padLeft($totalWidth, $paddingChar = ' ')
    {
        return new String(\str_pad($this->string, $totalWidth, $paddingChar, STR_PAD_LEFT));
    }

    public function padRight($totalWidth, $paddingChar = ' ')
    {
        return new String(\str_pad($this->string, $totalWidth, $paddingChar, STR_PAD_RIGHT));
    }

    public function remove($startIndex, $count = null)
    {
        $first = \substr($this->string, 0, $startIndex);

        if ($count === null) {
            return new String($first);
        }

        $second = \substr($this->string, $startIndex + $count);

        return new String($first.$second);
    }

    public function replace($old, $new)
    {
        return new String(\str_replace($old, $new, $this->string));
    }

    public function split($separator)
    {
        return \explode($separator, $this->string);
    }

    public function subString($startIndex, $length = null)
    {
        if ($length === null) {
            return new String(\substr($this->string, $startIndex));
        }

        return new String(\substr($this->string, $startIndex, $length));
    }

    public function toLower()
    {
        return new String(\strtolower($this->string));
    }

    public function toUpper()
    {
        return new String(\strtoupper($this->string));
    }

    public function toCharArray()
    {
        return $this->toArray();
    }

    public function trim($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(\trim($this->string));
        }

        return new String(\trim($this->string, $trimChars));
    }

    public function trimEnd($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(\rtrim($this->string));
        }

        return new String(\rtrim($this->string, $trimChars));
    }

    /**
     * @param null $trimChars
     * @return String
     */
    public function trimStart($trimChars = null)
    {
        if ($trimChars === null) {
            return new String(\ltrim($this->string));
        }

        return new String(\ltrim($this->string, $trimChars));
    }
}
