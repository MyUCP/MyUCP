<?php

class Header
{
    /**
     * Gets the HTTP headers.
     *
     * @param array $parameters
     * @return Collection
     */
    public static function getHeaders(array $parameters)
    {
        $headers = array();

        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);

        foreach ($parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }

        return new Collection($headers);
    }
}