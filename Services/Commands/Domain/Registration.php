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
 * Enom Domain Registration related operations
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 * @link   http://www.enom.com/APICommandCatalog/index.htm
 */
class Registration extends Enom
{
    /**
     * Check the availability of a domain name
     *
     * @param string $sld The second-level domain name to check (e.g. example, NOT example.com, or www.example.com)
     * @param string $tld The top-level domain name to check (e.g. com, NOT example.com, or www.example.com)
     *
     * @return SimpleXMLElement
     */
    public function checkDomain($sld, $tld)
    {
        $this->payload = array();
        $this->payload["SLD"] = $sld;
        if( strpos($tld, ',') !== false ) {
            $this->payload['TLDList'] = $tld;
        } else {
            $this->payload['TLD'] = $tld;
        }

        $command = 'Check';
        $data = $this->makeRequest($command, $this->payload);

        $result = array();
        if( !isset($data['DomainCount']) || '1' == $data['DomainCount'] ) {
            $result[$data['DomainName']] = ('210' == $data['RRPCode'])?true:false;
        } else {
            for($i=1; $i<=$data['DomainCount']; $i++) {
                $result[$data[('Domain'.$i)]] = ('210' == $data[('RRPCode'.$i)])?true:false;
            }
        }
        return $result;
    }

    /**
     * Retrieve the settings for email confirmations of orders
     *
     * @return SimpleXMLElement
     */
    public function getConfirmationSettings()
    {
        $this->payload = array();
        $command = 'GetConfirmationSettings';
        $data = $this->makeRequest($command, $this->payload);

        return $data->ConfirmationSettings;
    }

    /**
     * This command retrieves the extended attributes for a country code TLD (required parameters specific to the country code)
     *
     * @param string $tld The Country Code Top Level Domain to check for extended attributes
     *
     * @return SimpleXMLElement
     */
    public function getExtAttributes($tld)
    {
        $this->payload = array();
        // Strip out any leading periods, e.g. ".co.uk" or ".de"
        $tld = ltrim($tld, " .");
        if (empty($tld)) {
            throw new \InvalidArgumentException("TLD cannot be empty");
        }

        $this->payload["tld"] = $tld;

        $command = 'GetExtAttributes';
        $data = $this->makeRequest($command, $this->payload);

        return $data->Attributes;
    }

   /**
    * Register a new domain name
    *
    * Executes the 'Purchase' command on Enom's servers to register a new domain.
    * Note that this command to not fail, it must meet the following requirements:
    *     - Your Enom account must have enough credits to cover the order amount.
    *     - The domain name must be valid and available.
    *     - Number of years must not be less than the minimum number of years required for the specified TLD.
    *     - Name Servers must be valid and registered.
    *     - Name servers for .us names must be located in the United States.
    *     - RegistrantJobTitle and RegistrantFax are required in the contacts array if RegistrantOrganizationName is set.
    *
    * @param      string      $domainName     Must be a valid domain name, that is currently available
    * @param      int         $numYears       Some TLDs like .co.uk requires minimum of 2 years, Another may require 10 years
    * @param      array       $contacts       Associative array containing Contacts as key and value.
    * @param      array       $nameServers    If not set, Enom's Default name servers will be used instead.
    * @param      bool        $regLock        A flag that specifies if the domain should be locked or not. Default is true.
    * @return     long        Order ID, or false if failed.
    * @access     public
    * @see        renewDomain
    * @see        transferDomain
    */
    public function registerDomain($domain, $numYears=1, $contacts=null, $nameServers=null, $regLock=true, $password = null)
    {
        $this->payload = array();
        $domain = trim($domain);
        if (empty($domain)) {
            throw new \InvalidArgumentException("Domain cannot be empty");
        }
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload["SLD"] = $domainArr['sld'];
        $this->payload["TLD"] = $domainArr['tld'];

        $this->payload["NumYears"] = (int) $numYears;

        if (is_array($nameServers) && (count($nameServers) > 0)) {
            foreach ($nameServers as $i => $server) {
                $this->payload["NS".($i+1)] = $server;
            }
        } else {
            $this->payload["UseDNS"] = "default";
        }

        $this->payload["UnLockRegistrar"] = (int) $regLock;

        if ( !is_null($contacts) ) {
            if( isset($contacts['EmailAddress']) ) {
                $contacts = array('registrant'=>$contacts);
            } else {
                foreach($contacts as $type=>$info) {
                    switch($type) {
                        case 'registrant':
                            $typeName = 'Registrant';
                            break;
                        case 'billing':
                            $typeName = 'Billing';
                            break;
                        case 'auxbilling':
                            $typeName = 'AuxBilling';
                            break;
                        case 'tech':
                            $typeName = 'Tech';
                            break;
                        case 'admin':
                            $typeName = 'Admin';
                            break;
                        default:
                            break;
                    }
                    $this->payload[$typeName.'EmailAddress'] = $info['EmailAddress'];
                    $this->payload[$typeName.'Fax'] = $info['Fax'];
                    $this->payload[$typeName.'Phone'] = $info['Phone'];
                    $this->payload[$typeName.'Country'] = $info['Country'];
                    $this->payload[$typeName.'PostalCode'] = $info['PostalCode'];
                    if( $info['StateProvinceChoice'] == 'S' ) {
                        $this->payload[$typeName.'StateProvinceChoice'] = 'S';
                        $this->payload[$typeName.'StateProvince'] = $info['State'];
                    } elseif( $info['StateProvinceChoice'] == 'P' ) {
                        $this->payload[$typeName.'StateProvinceChoice'] = 'Province';
                        $this->payload[$typeName.'StateProvince'] = $info['Province'];
                    } else {
                        $this->payload[$typeName.'StateProvinceChoice'] = 'Blank';
                        $this->payload[$typeName.'StateProvince'] = '';
                    }
                    $this->payload[$typeName.'City'] = $info['City'];
                    $this->payload[$typeName.'Address1'] = $info['Address1'];
                    $this->payload[$typeName.'Address2'] = $info['Address2'];
                    $this->payload[$typeName.'LastName'] = $info['LastName'];
                    $this->payload[$typeName.'FirstName'] = $info['FirstName'];
                    $this->payload[$typeName.'JobTitle'] = $info['JobTitle'];
                    $this->payload[$typeName.'OrganizationName'] = $info['OrganizationName'];
                }
            }
        }

        $command = 'Purchase';
        $data = $this->makeRequest($command, $this->payload);

        return $data['OrderID'];
    }

   /**
    * Renew a domain name that belongs to your Enom account
    *
    * Executes the 'Extend' command on Enom's servers to renew a domain name which was previously registered or transfered to your Enom account.
    * Note that this command to not fail, it must meet the following requirements:
    *     - Your Enom account must have enough credits to cover the order amount.
    *     - The domain name must be valid and active and belongs to your Enom account.
    *     - The new expiration date cannot be more than 10 years in the future.
    *
    * @param      string      $domainName     Must be a valid and active domain name.
    * @param      int         $numYears       The new expiration date cannot be more than 10 years in the future.
    * @return     long        Renewal Order ID, or false if failed.
    * @access     public
    * @see        registerDomain
    * @see        transferDomain
    */
    public function renewDomain($domain, $numYears)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        $this->payload['NumYears'] = $numYears;

        $command = 'Extend';
        $data = $this->makeRequest($command, $this->payload);

        return $data['OrderID'];
    }

   /**
    * Get expiry date for a domain name
    *
    * Executes the 'GetDomainExp' command on Enom's servers, to retrive the expiration date
    * of a domain name that is active and belongs to your Enom account.
    *
    * @param      string      $domainName         Must be active and belongs to your Enom account.
    * @return     string      Expiration date, or false on fail
    * @access     public
    * @see        getDomainNameID
    */
    public function getExpiryDate($domain)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        $command = 'GetDomainExp';
        $data = $this->makeRequest($command, $this->payload);

        return $data['ExpirationDate'];
    }


   /**
    * Get name servers for a domain name.
    *
    * Executes the 'GetDNS' command on Enom's servers, to retrive the name servers
    * for a domain name that is active and belongs to your Enom account.
    *
    * @param      string      $domainName         Must be active and belongs to your Enom account.
    * @return     array       An array containing name servers. If using Enom's name servers, the array will be empty.
    * @access     public
    * @see        getNameServers
    */
    public function getNameServers($domain)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        $command = 'GetDNS';
        $data = $this->makeRequest($command, $this->payload);

        $nameservers = array();
        if( 'default' == strToLower($data['UseDNS']) ) {
            return $nameservers;
        }
        for($i=1; $i<=$data['NSCount']; $i++) {
            $nameservers[] = $data[ ('DNS'.$i) ];
        }
        return $nameservers;
    }

    /**
    * Set name servers for a domain name.
    *
    * Executes the 'ModifyNS' command on Enom's servers, to set the name servers
    * for a domain name that is active and belongs to your Enom account.
    *
    * @param      string      $domainName         Must be active and belongs to your Enom account.
    * @param      array       $nameservers        Array containing name servers. If not set, default Enom name servers will be used.
    * @return     bool        True if succeed and false if failed.
    * @access     public
    * @see        getNameServers
    */
    public function setNameServers($domain, $nameservers = null)
    {
        $this->payload = array();
        $domainArr = $this->_parseDomain($domain);
        if( !$domainArr ) {
            return new \InvalidArgumentException("Invalid domain name $domain");
        }
        $this->payload['SLD'] = $domainArr['sld'];
        $this->payload['TLD'] = $domainArr['tld'];

        if( !is_null($nameservers) ) {
            $i = 1;
            foreach($nameservers as $ns_server) {
                $this->payload['NS'.$i] = $ns_server;
                $i++;
            }
        } else {
            $this->payload['UseDNS'] = 'Default';
        }

        $command = 'ModifyNS';
        $data = $this->makeRequest($command, $this->payload);

        return true;
    }
}
