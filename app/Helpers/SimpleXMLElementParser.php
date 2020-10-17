<?php

namespace App\Helpers;

use SimpleXMLElement;

class SimpleXMLElementParser
{
    /**
     * Parse a SimpleXmlElement object to an array
     *
     * @param $element
     * @return array
     */
    public static function parseToArray($element): ?array
    {
        foreach ((array) $element as $key => $value) {
            $result[$key] = is_object($value) || is_array($value) ? self::parseToArray($value) : $value;
        }

        return $result ?? null;
    }
}
