<?php

namespace App\Utils;

use SimpleXMLElement;
use Psr\Http\Message\ResponseInterface;

class RequestParser {

    /**
     * Parse a XML response to a object
     *
     * @param ResponseInterface $response
     * @return object
     */
    public static function parse(ResponseInterface $response): object
    {
        return new SimpleXMLElement($response->getBody()->getContents());
    }
}