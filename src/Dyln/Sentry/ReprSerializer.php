<?php
/**
 * Created by PhpStorm.
 * User: bill
 * Date: 30/09/18
 * Time: 21:03
 */

namespace Dyln\Sentry;


class ReprSerializer extends \Raven_ReprSerializer
{
    protected function serializeValue($value)
    {
        if ($value === null) {
            return 'null';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === true) {
            return 'true';
        } elseif (is_float($value) && (int) $value == $value) {
            return $value . '.0';
        } elseif (is_integer($value) || is_float($value)) {
            return (string) $value;
        } elseif (is_object($value) || gettype($value) == 'object') {
            if ($value instanceof ReprInfoProvider) {
                return $this->serialize($value->provideReprInfo());
            }
            if (method_exists($value, '__toString')) {
                return $this->serialize(['class' => get_class($value), 'payload' => (string) $value, 'note' => 'by \\Dyln\\Sentry\\ReprSerializer']);
            }
            if (method_exists($value, '__toArray')) {
                return $this->serialize(['class' => get_class($value), 'payload' => (array) $value, 'note' => 'by \\Dyln\\Sentry\\ReprSerializer']);
            }
            if (method_exists($value, 'toArray')) {
                return $this->serialize(['class' => get_class($value), 'payload' => $value->toArray(), 'note' => 'by \\Dyln\\Sentry\\ReprSerializer']);
            }

            return 'Object ' . get_class($value);
        } elseif (is_resource($value)) {
            return 'Resource ' . get_resource_type($value);
        } elseif (is_array($value)) {
            return 'Array of length ' . count($value);
        } else {
            return $this->serializeString($value);
        }
    }

}