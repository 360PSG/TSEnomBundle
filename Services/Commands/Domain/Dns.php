<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TS\Bundle\EnomBundle\Services\Commands\Domain;

use TS\Bundle\EnomBundle\Services\Enom;

/**
 * Enom Domain DNS related operations
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 * @link   http://www.enom.com/APICommandCatalog/index.htm
 */
class Dns extends Enom
{
    /**
     * Get the DNS records or a given domain name
     *
     * @param string $domain The domain name to check (e.g. example, NOT example.com, or www.example.com)
     *
     * @return SimpleXMLElement
     */
    public function getHosts($domain)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        $command = 'GetHosts';
        $data = $this->makeRequest($command, $this->payload);

        $results = array();
        for ( $i = 1; $i <= $data['HostCount']; $i++ ) {
            $tmp_arr = array();
            $tmp_arr['HostId'] = $data['hostid'.$i];
            $tmp_arr['HostName'] = $data['HostName'.$i];
            $tmp_arr['RecordType'] = $data['RecordType'.$i];
            $tmp_arr['Address'] = $data['Address'.$i];
            if ( $tmp_arr['RecordType'] == 'MX' ) {
                $tmp_arr['MxPref'] = $data['MxPref'.$i];
            }
            $results[$data['hostid'.$i]] = $tmp_arr;
        }
        return $results;
    }

    /**
     * Set the DNS records or a given domain name
     *
     * @param string $domain The domain name to check (e.g. example, NOT example.com, or www.example.com)
     * @param array $dns DNS Records to set
     *
     * @return SimpleXMLElement
     */
    public function setHosts($domain, $dns)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        $i = 1;
        foreach($dns as $record) {
            $this->payload['HostName'.$i]   = $record['HostName'];
            $this->payload['Address'.$i]    = $record['Address'];
            $this->payload['RecordType'.$i] = $record['RecordType'];
            if ( isset($record['MxPref']) ) {
                $this->payload['MxPref'.$i] = $record['MxPref'];
            }
            $i++;
        }

        $command = 'SetHosts';
        $data = $this->makeRequest($command, $this->payload);

        return true;
    }

}
