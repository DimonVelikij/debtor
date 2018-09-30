<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('admin.yml');
        $loader->load('validator.yml');
        $loader->load('service.yml');
        $loader->load('event_generator.yml');
        $loader->load('twig.yml');
        $loader->load('form_admin.yml');
    }
}