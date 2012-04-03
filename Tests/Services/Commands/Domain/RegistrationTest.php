<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SOG\EnomBundle\Tests\Services\Commands\Domain;

use SOG\EnomBundle\Services\Enom;

/**
 * Test Enom Domain registration commands
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class RegistrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Enom Domain Registration class
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration
     */
    public function testRegistration()
    {
        $enom = $this
                ->getMockBuilder('SOG\EnomBundle\Services\Commands\Domain\Registration')
                ->disableOriginalConstructor()->getMock();
    }

    /**
     * Test check domain availability
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration::check
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationCheckArray()
    {
        // Use live Enom test credentials
        $enom = new Enom('http://resellertest.enom.com', 'resellerid', 'resellpw');

        $domains = array("adomainname.com", "adomainname2.com");

        $data = $enom->getDomainRegistration()->check($domains);
    }

    /**
     * Test check domain availability
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration::check
     */
    public function testRegistrationCheck()
    {
        // Use live Enom test credentials
        $enom = new Enom('http://resellertest.enom.com', 'resellerid', 'resellpw');

        $data = $enom->getDomainRegistration()->check("adomainname.com");

        $this->assertEquals(211, (int) $data->RRPCode);
        $this->assertEquals(1, (int) $data->MinPeriod);
        $this->assertEquals(10, (int) $data->MaxPeriod);

    }

    /**
     * Test email order confirmation settings
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration::getConfirmationSettings
     */
    public function testRegistrationGetConfirmationSettings()
    {
        // Use live Enom test credentials
        $enom = new Enom('http://resellertest.enom.com', 'resellid', 'resellpw');

        $data = $enom->getDomainRegistration()->getConfirmationSettings();

        $this->assertEquals("True", $data->OrderConfirmation);
        $this->assertEquals("True", $data->TransferOrderConfirmation);
        $this->assertEquals("True", $data->OrderConfirmationBCC);
        $this->assertEquals("True", $data->TransferOrderConfirmationBCC);
        $this->assertEquals("&#12486;&#12473;&#12488;", $data->EmailHead);
        $this->assertEquals("&#12486;&#12473;&#12488;", $data->EmailTail);

    }

    /**
     * Test extended attributes, when empty parameter passed
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration::getExtAttributes
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationExtAttributesEmpty()
    {
        // Use live Enom test credentials
        $enom = new Enom('http://resellertest.enom.com', 'resellerid', 'resellpw');

        // Pass in a whitespace string to ensure it's getting trimmed
        $data = $enom->getDomainRegistration()->getExtAttributes(" ");

    }

    /**
     * Test extended attributes
     *
     * @covers SOG\EnomBundle\Services\Commands\Domain\Registration::getExtAttributes
     */
    public function testRegistrationExtAttributes()
    {
        // Use live Enom test credentials
        $enom = new Enom('http://resellertest.enom.com', 'resellerid', 'resellpw');

        $data = $enom->getDomainRegistration()->getExtAttributes("co.uk");

        // Ensure we have 4 attributes for .co.uk TLD
        $this->assertEquals(4, $data->children()->count());
    }
}