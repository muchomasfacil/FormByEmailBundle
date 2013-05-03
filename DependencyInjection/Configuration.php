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
                            ->arrayNode('sender_setFrom')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('email')->end()
                                        ->scalarNode('name')->end()
                                    ->end()                                    
                                ->end()
                            ->end()
                            
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
/*        $rootNode
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
                        ->arrayNode('sender_setFrom')                                                       
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()->end()
                        //->arrayNode('sender_setSender')
                        //    ->prototype('scalar')->end()
                        //->end()
                        //->arrayNode('sender_setReturnPath')
                        //    ->prototype('scalar')->end()
                        //->end()
                        //->arrayNode('sender_setReplyTo')
                        //    ->prototype('scalar')->end()
                        //->end()
                        ->arrayNode('sender_setTo')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender_setCc')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender_setBcc')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender_addTo')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender_addCc')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sender_addBcc')
                            ->prototype('array')
                            ->children()                      
                                ->scalarNode('email')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;                  
*/        
        
        
        return $treeBuilder;
    }
}
