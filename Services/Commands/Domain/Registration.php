<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Services\Commands\Domain;

use SOG\EnomBundle\Services\HttpClient;

/**
 * Enom Domain Registration related operations
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 * @link   http://www.enom.com/APICommandCatalog/index.htm
 */
class Registration extends HttpClient
{
    /**
     * Check the availability of a domain name
     *
     * @param string $domain The domain name to check (e.g. example.com, NOT www.example.com)
     *
     * @return SimpleXMLElement
     */
    public function check($domain)
    {
        if (is_array($domain)) {
            throw new \InvalidArgumentException("Multiple domain name checks are not allowed");
        }

        $pieces = explode(".", $domain);

        $this->payload["sld"] = $pieces[0];
        $this->payload["tld"] = $pieces[1];

        $command = 'Check';
        $data = $this->makeRequest($command, $this->payload);

        return $data;
    }
}