<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\Module;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Subitems module.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          content_tree_module:
 *              load_more_limit: 30
 *              children_load_max_limit: 200
 *              tree_max_depth: 10
 * ```
 */
class ContentTree extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_tree_module')
                ->info('Content Tree module configuration')
                ->children()
                    ->integerNode('load_more_limit')
                        ->info('Number of children to load in expand and load more operations')
                        ->isRequired()
                        ->defaultValue(30)
                    ->end()
                    ->integerNode('children_load_max_limit')
                        ->info('Total limit of loaded children in single node')
                        ->isRequired()
                        ->defaultValue(200)
                    ->end()
                    ->integerNode('tree_max_depth')
                        ->info('Max depth of expanded tree')
                        ->isRequired()
                        ->defaultValue(10)
                    ->end()
                    ->arrayNode('content_type_ignore_list')
                        ->info('List of content type identifiers to ignore in Content Tree')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('content_type_identifier')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['content_tree_module'])) {
            return;
        }

        $settings = $scopeSettings['content_tree_module'];

        if (array_key_exists('load_more_limit', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.load_more_limit',
                $currentScope,
                $settings['load_more_limit']
            );
        }

        if (array_key_exists('children_load_max_limit', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.children_load_max_limit',
                $currentScope,
                $settings['children_load_max_limit']
            );
        }

        if (array_key_exists('tree_max_depth', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.tree_max_depth',
                $currentScope,
                $settings['tree_max_depth']
            );
        }

        if (array_key_exists('content_type_ignore_list', $settings)) {
            $contextualizer->setContextualParameter(
                'content_tree_module.content_type_ignore_list',
                $currentScope,
                $settings['content_type_ignore_list']
            );
        }
    }
}
