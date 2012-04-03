<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Tests\Services\Commands;

use SOG\EnomBundle\Services\Enom;

/**
 * Test Enom Account commands
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Enom account class
     *
     * @covers SOG\EnomBundle\Services\Command\Account
     */
    public function testAccount()
    {
        $enom = $this
                ->getMockBuilder('SOG\EnomBundle\Services\Commands\Account')
                ->disableOriginalConstructor()->getMock();
    }

    /**
     * Test getAccountInfo
     *
     * @covers SOG\EnomBundle\Services\Enom::getAccountInfo
     */
    public function testGetAccountInfo()
    {
        // Use live Enom test credentials
        $enom = new Enom('https://resellertest.enom.com', 'resellerid', 'resellpw');

        $data = $enom->getAccount()->getAccountInfo();

        $this->assertEquals("resellerid", $data->UserID);
        $this->assertEquals("resellpw", $data->Password);
        $this->assertEquals("None", $data->AuthQuestionType);
        $this->assertEquals("957-ws-7113", $data->Account);
        $this->assertEquals("False", $data->Reseller);
        $this->assertEquals("False", $data->AcceptTerms);
    }

    /**
     * Test getTldList
     *
     * @covers SOG\EnomBundle\Services\Enom::getTldList
     */
    public function testGetTldList()
    {
        // Use live Enom test credentials
        $enom = new Enom('https://resellertest.enom.com', 'resellerid', 'resellpw');

        $data = $enom->getAccount()->getTldList();

        // With 104 results I'm not going to write an assertEquals for each of  them
        $this->assertEquals(104, (int)$data->tldcount);
    }
}