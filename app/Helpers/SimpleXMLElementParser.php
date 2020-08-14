<?php

namespace App\Helpers;

use SimpleXMLElement;

class SimpleXMLElementParser {

    /**
     * Parse a SimpleXmlElement object to an array
     *
     * @param SimpleXMLElement $element
     * @return array
     */
    public static function parseToArray( SimpleXMLElement $element ): array
    {
        foreach ( (array) $element as $key => $value ) {
            $result[$key] = is_object($value) ? self::parseToArray($value) : $value;
        }
        
        return $result;
    }
}