<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Services\Commands;

use SOG\EnomBundle\Services\HttpClient;

/**
 * Enom Account Related operations
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 * @link   http://www.enom.com/APICommandCatalog/index.htm
 */
class Account extends HttpClient
{
    private $payload = array();

    /**
     * Get Account Info
     *
     * @return SimpleXMLElement Account Information
     */
    public function getAccountInfo()
    {
        $command = 'GetAccountInfo';
        $data = $this->makeRequest($command, $this->payload);

        return $data->GetAccountInfo;
    }

    /**
     * Get TLD list
     *
     * @return SimpleXMLElement
     */
    public function getTldList()
    {
        $command = 'GetTLDList';
        $data = $this->makeRequest($command, $this->payload);

        return $data->tldlist;
    }

    /**
     * Retrieve the customer service contact information for a domain name account.
     *
     * @return SimpleXMLElement
     */
    public function getServiceContact()
    {
        $command = 'GetServiceContact';
        $data = $this->makeRequest($command, $this->payload);

        return $data->ServiceContact;
    }
}