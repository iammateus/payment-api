<?php

namespace App\Utils;

class ApiToken
{
    /**
     * Generates an api token encoded in md5 and base64
     *
     * @param string $content 
     * @return string
     */
    public static function generate(string $content): string
    {
        $md5Content = md5($content);
        return base64_encode($md5Content);
    } 
}