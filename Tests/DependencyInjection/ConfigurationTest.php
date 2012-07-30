<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TS\Bundle\EnomBundle\Tests\DependencyInjection;

use TS\Bundle\EnomBundle\DependencyInjection\Configuration;

/**
 * Test Configuration
 *
 * @author Shane O'Grady <shane.ogrady@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test can get config tree
     *
     * @covers TS\Bundle\EnomBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testThatCanGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $configuration->getConfigTreeBuilder());
    }
}
