<?php

/*
 * This file is part of the SOG/EnomBundle
 *
 * (c) Shane O'Grady <shane.ogrady@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace TS\Bundle\EnomBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * @return treeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ts_enom');

        $rootNode->children()
                ->scalarNode('url')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('username')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('password')
                ->isRequired()
                ->cannotBeEmpty()
                ->end()
                ->end();

        return $treeBuilder;
    }
}
