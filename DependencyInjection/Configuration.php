<?php

namespace MuchoMasFacil\FormByEmailBundle\DependencyInjection;

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
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mucho_mas_facil_form_by_email');

        $rootNode
            ->children()
                ->arrayNode('definitions')
                ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->end()
                            ->booleanNode('is_html')->end()
                            ->scalarNode('locale')->end()
                            ->scalarNode('translation_domain')->end()
                            ->arrayNode('skip_fields')
                                ->prototype('scalar')->end()
                            ->end()
                            ->variableNode('sender_setFrom')->end()
                            ->scalarNode('sender_setSender')->end()
                            ->scalarNode('sender_setReturnPath')->end()
                            ->scalarNode('sender_setReplyTo')->end()
                            ->variableNode('recipients_setTo')->end()
                            ->variableNode('recipients_setCc')->end()
                            ->variableNode('recipients_setBcc')->end()
                            ->variableNode('recipients_addTo')->end()
                            ->variableNode('recipients_addCc')->end()
                            ->variableNode('recipients_addBcc')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;       
        
        return $treeBuilder;
    }
}
