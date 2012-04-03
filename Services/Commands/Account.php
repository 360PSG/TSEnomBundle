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

    /**
     * Get a list of the orders placed through this account.
     * Return sets of 25 records in reverse chronological order
     *
     * @param string $start The record to begin at (i.e. $start=26 returns the 26th through 50th most recent orders)
     * @param string $begin MM/DD/YYYY Beginning date of orders to return
     * @param string $end   MM/DD/YYYY End date of orders to return
     *
     * @return SimpleXMLElement
     */
    public function getOrderList($start = 1, $begin = null, $end = null)
    {
        $this->payload['start'] = $start;

        if (isset($begin)) {
            $this->payload['begindate'] = $begin;
        }

        if (isset($end)) {
            $this->payload['enddate'] = $end;
        }

        $command = 'GetOrderList';
        $data = $this->makeRequest($command, $this->payload);

        return $data->OrderList;
    }
}