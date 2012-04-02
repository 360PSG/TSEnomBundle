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
 * Enom
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class Enom
{

    private $url;
    private $username;
    private $password;

    /**
     * Initializes Enom
     *
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

        if (!function_exists('curl_init')) {
            throw new \Exception('This bundle needs the cURL PHP extension.');
        }

        if (!extension_loaded('simplexml')) {
            throw new \Exception('This bundle needs the SimpleXML PHP extension.');
        }
    }

    /**
     * Get Enom reseller url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get Enom Account ID
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Get Enom Account password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


}
