<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Tests\Services;

use SOG\EnomBundle\Services\HttpClient;

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
     * @covers SOG\EnomBundle\Services\HttpClient
     */
    public function testHttpClient()
    {
        $client = $this
                ->getMockBuilder('SOG\EnomBundle\Services\HttpClient')
                ->disableOriginalConstructor()->getMock();
    }

    /**
     * Test HttpClient constructor
     *
     * @covers SOG\EnomBundle\Services\HttpClient::__construct
     */
    public function testHttpClientConstruct()
    {
        $client = new HttpClient('https://reseller.enom.com', 'reseller_uid', 'reseller_pw');

        $this->assertNotNull($client);
        $this->assertInstanceOf("SOG\EnomBundle\Services\HttpClient", $client);

    }

}
