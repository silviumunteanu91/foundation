<?php

namespace Dyln\Util;

class ArrayUtil
{
    public static function getFirstElement(array $array, $remove = false)
    {
        if ($remove) {
            return array_shift($array);
        }

        return array_shift(array_values($array));
    }

    public static function getLastElement(array $array, $remove = false)
    {
        if ($remove) {
            return array_pop($array);
        }

        return array_pop(array_values($array));
    }

    public static function getIn(array $array, $keys, $default = null)
    {
        if (is_null($keys)) {
            return $array;
        }
        if (!is_array($keys)) {
            if (strpos($keys, '.') !== false) {
                $keys = explode('.', $keys);
            } else {
                $keys = [$keys];
            }
        }

        $current = $array;
        foreach ($keys as $key) {
            if (!is_array($current)) {
                return $default;
            }
            if (!array_key_exists($key, $current)) {
                return $default;
            }

            $current = $current[$key];
        }

        return is_null($current) ? $default : $current;
    }

    public static function has($array, $key)
    {
        if (is_array($key)) {
            $key = implode('.', $key);
        }
        if (empty($array) || is_null($key)) {
            return false;
        }
        if (array_key_exists($key, $array)) {
            return true;
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    public static function isAssoc(array $array)
    {
        if ([] === $array) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
