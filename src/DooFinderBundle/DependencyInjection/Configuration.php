<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package DooFinderBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dooFinder');
        $rootNode
            ->children()
            ->variableNode('search_api_key')->isRequired()->end()
            ->variableNode('management_api_key')->isRequired()->end()
            ->arrayNode('search_engines')
            ->arrayPrototype()
            ->children()
            ->variableNode('name')->isRequired()->end()
            ->variableNode('type')->end()
            ->variableNode('site_url')->end()
            ->variableNode('language')->end()
            ->variableNode('currency')->end()
            ->variableNode('id')->end()
            ->variableNode('hashId')->end()
            ->variableNode('user')->end()
            ->variableNode('objectPathRegex')->defaultNull()->end()
            ->booleanNode('active')->defaultValue(true)->end()
            ->variableNode('baseURL')->isRequired()->end()
            ->arrayNode('item')->isRequired()
            ->children()
            ->variableNode('class')->isRequired()->end()
            ->variableNode('listing')->isRequired()->end()
            ->arrayNode('listing_arguments')
            ->arrayPrototype()
            ->children()
            ->variableNode('unpublished')->end()
            ->variableNode('condition')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('fields')->isRequired()
            ->arrayPrototype()
            ->children()
            ->variableNode('dfAttribute')->end()
            ->variableNode('classAttribute')->end()
            ->variableNode('brickAttribute')->end()
            ->arrayNode('imageAttribute')
            ->arrayPrototype()
            ->children()
            ->variableNode('field')->end()
            ->scalarNode('index')->end()
            ->variableNode('thumbnail')->end()
            ->end()
            ->end()
            ->end()
            ->variableNode('locale')->end()
            ->variableNode('getter')->end()
            ->variableNode('postfix')->end()
            ->variableNode('prefix')->end()
            ->arrayNode('merger')
            ->arrayPrototype()
            ->children()
            ->variableNode('class')->end()
            ->variableNode('field')->end()
            ->arrayNode('options')
            ->arrayPrototype()
            ->children()
            ->variableNode('locale')->end()
            ->variableNode('baseUrl')->end()
            ->variableNode('currency')->end()
            ->variableNode('thumbnail')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('url')
            ->arrayPrototype()
            ->children()
            ->variableNode('class')->end()
            ->variableNode('route')->end()
            ->variableNode('prefix')->end()
            ->variableNode('locale')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}