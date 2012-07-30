<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TS\Bundle\EnomBundle\Tests\Services;

use TS\Bundle\EnomBundle\Services\HttpClient;

/**
 * Test HttpClient
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test HttpClient class
     *
     * @covers TS\Bundle\EnomBundle\Services\HttpClient
     */
    public function testHttpClient()
    {
        $client = $this
                ->getMockBuilder('TS\Bundle\EnomBundle\Services\HttpClient')
                ->disableOriginalConstructor()->getMock();
    }

    /**
     * Test HttpClient constructor
     *
     * @covers TS\Bundle\EnomBundle\Services\HttpClient::__construct
     */
    public function testHttpClientConstruct()
    {
        $client = new HttpClient('https://reseller.enom.com', 'reseller_uid', 'reseller_pw');

        $this->assertNotNull($client);
        $this->assertInstanceOf("TS\Bundle\EnomBundle\Services\HttpClient", $client);

    }

}
