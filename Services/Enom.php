<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TS\Bundle\EnomBundle\Services;

use TS\Bundle\EnomBundle\Services\EnomException;

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
     * Send API request to Enom
     *
     * @param string $command Enom API command/method
     * @param string $payload Request information
     *
     * @return SimpleXMLElement
     */
    protected function makeRequest($command, $payload)
    {
        $payload['command'] = $command;
        $payload['uid']     = $this->username;
        $payload['pw']      = $this->password;

        $url = $this->url . '/interface.asp?' . http_build_query($payload);
        if ( strlen($url) >= 2092 ) {
            if ( $command == 'SetHosts' ) {
                throw new EnomException("Your records have reached our DNS provider's maximum capacity, your request could not be completed.");
            } else {
                throw new EnomException("Your request could not be completed.");
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "eNom API");
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $results = curl_exec($ch);
        curl_close($ch);

        $results = explode("\n", $results);
        $return_vars = array();
        foreach( $results as $result ) {
            $result = trim($result);
            if ( $result != '' && substr($result, 0, 1) != ';' ) {
                list($key, $val) = explode("=", $result, 2);
                $return_vars[$key] = $val;
            }
        }

        if ((isset($return_vars['ErrCount'])) && ((int) $return_vars['ErrCount'] > 0)) {
            throw new EnomException($return_vars['Err1']);
        }

        return $return_vars;
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

    /**
     * Get Account commands
     *
     * @return Commands\Account
     */
    public function getAccount()
    {
        return new Commands\Account($this->url, $this->username, $this->password);
    }

    /**
     * Get Domain Registration commands
     *
     * @return Commands\Domain\Registration
     */
    public function getDomainRegistration()
    {
        return new Commands\Domain\Registration($this->url, $this->username, $this->password);
    }

    /**
     * Get Domain DNS commands
     *
     * @return Commands\Domain\Dns
     */
    public function getDomainDns()
    {
        return new Commands\Domain\Dns($this->url, $this->username, $this->password);
    }

   /**
    * Check if the passed SLD (Second-level domain) is valid or not
    *
    * Enom says in it's API documentations, that valid SLD must meet the following requirements:
    *
    *     - Must be composed of the letters a through z, the numbers 0 through 9, and the hyphen (-) character.
    *     - Some foreign character sets can display onscreen, but resolve to alphanumeric plus hyphen characters in the underlying code.
    *     - must not begin or end with the hyphen character.
    *     - must not contain spaces.
    *     - must not contain special characters other than the hyphen character.
    *     - The third and fourth characters must not both be hyphens unless it is an encoded international-character domain name.
    *     - must contain 2 to 63 characters, inclusive.
    *
    * @param      string      $sld
    * @return     bool                    Return true if valid, Otherwise it return false
    * @access     private
    * @see        _setParseDomain
    */
    protected function _isValidSLD($sld)
    {
        if( preg_match('/^[a-z0-9]+[a-z0-9\-]*[a-z0-9]+$/i', $sld) && strlen($sld) < 64 && substr($sld, 2, 2) != '--' ) {
            return true;
        }
        return false;
    }

   /**
    * Parse the passed domain name and return as Array
    *
    * This function will check first if the passed domain name is valid or not, and returns false if not valid,
    * Otherwise it will set the TLD and SLD parameters to the parsed matches.
    *
    * @param      string      $domainName
    * @return     bool                    Return true if valid, Otherwise it return false
    * @access     private
    * @see        _isValidSLD
    */
    protected function _parseDomain($domainName)
    {
        if ( !preg_match('/^([a-z0-9]+[a-z0-9\-]*[a-z0-9]+)\.([a-z]+[a-z]*[a-z]+)$/i', $domainName, $parts) || !$this->_isValidSLD($parts[1]) ) {
            return false;
        }
        return array('sld'=>$parts[1], 'tld'=>$parts[2]);
    }
}
