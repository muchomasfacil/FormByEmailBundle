<?php

namespace MuchoMasFacil\FormByEmailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MuchoMasFacilFormByEmailExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //first load definitions in services.yml
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        //and add them to our list
        $parameter_configs[] = array(
            'definitions'  => $container->getParameter('mucho_mas_facil_form_by_email.definitions'),            
            );
        //now take configurations
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        //and add them to our list
        $parameter_configs[] = $config;
        $final_config = $this->processConfiguration($configuration, $parameter_configs);

        $container->setParameter('mucho_mas_facil_form_by_email', $final_config['definitions']); 
    }
}
