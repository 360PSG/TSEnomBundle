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

use TS\Bundle\EnomBundle\Services\Enom;

/**
 * Test enom
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class EnomTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Enom class
     *
     * @covers TS\Bundle\EnomBundle\Services\Enom
     */
    public function testEnom()
    {
        $enom = $this
                ->getMockBuilder('TS\Bundle\EnomBundle\Services\Enom')
                ->disableOriginalConstructor()->getMock();
    }

    /**
     * Test Enom constructor
     *
     * @covers TS\Bundle\EnomBundle\Services\Enom::__construct
     */
    public function testEnomConstruct()
    {
        $enom = new Enom('https://reseller.enom.com', 'reseller_uid', 'reseller_pw');

        $this->assertEquals('https://reseller.enom.com', $enom->getUrl());
        $this->assertEquals('reseller_uid', $enom->getUsername());
        $this->assertEquals('reseller_pw', $enom->getPassword());

    }

}
