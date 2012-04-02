<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Services;

/**
 * cURL client
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class HttpClient
{
    protected $url;
    protected $username;
    protected $password;

    /**
     * Initializes Http client
     *
     * @param string $url      Enom reseller URL
     * @param string $username Enom Account login ID
     * @param string $password Enom Account password
     */
    public function __construct($url, $username, $password)
    {
        $this->url      = $url;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Send API request to Enom
     *
     * @param string $command Enom API command/method
     * @param string $payload Request information
     *
     * @return json
     */
    protected function makeRequest($command, $payload)
    {
        $payload['command'] = $command;
        $payload['uid']     = $this->username;
        $payload['pw']      = $this->password;

        $url = $this->url . '/interface.asp?' . http_build_query($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "SOGEnomBundle");
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $this->xml2json($result);
    }

    /**
     * Convert the returned XML to JSON
     *
     * @param string $xml The Enom XML to convert to JSON
     *
     * @return json
     */
    private function xml2json($xml)
    {
        $array = json_decode(json_encode($xml), true);

        foreach (array_slice($array, 0) as $key => $value) {
            if (empty($value)) {
                $array[$key] = null;
            }
            elseif (is_array($value)) {
                $array[$key] = toArray($value);
            }
        }

       return json_encode($array);
    }

}
