<?php

namespace App\Utils;

use SimpleXMLElement;
use Psr\Http\Message\ResponseInterface;

class ResponseParser {

    /**
     * Parse a XML response to a object
     *
     * @param ResponseInterface $response
     * @return object
     */
    public static function parseXml(ResponseInterface $response): object
    {
        return new SimpleXMLElement($response->getBody()->getContents());
    }
}