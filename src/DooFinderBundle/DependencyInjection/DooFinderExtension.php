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


use DooFinderBundle\Configuration\Configuration as BundleConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DooFinderExtension
 * @package DooFinderBundle\DependencyInjection
 */
class DooFinderExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yml');


        $configManagerDefinition = $container->getDefinition(BundleConfiguration::class);
        $configManagerDefinition->addMethodCall('setConfig', [$config]);

        if (file_exists(BundleConfiguration::SYSTEM_CONFIG_FILE_PATH)) {
            $bundleConfig = Yaml::parse(file_get_contents(BundleConfiguration::SYSTEM_CONFIG_FILE_PATH));
            $configManagerDefinition->addMethodCall('setSystemConfig', [$bundleConfig]);
        }

        $container->setParameter('doo_finder_search_api_key', $config['search_api_key']);
        $container->setParameter('doo_finder_management_api_key', $config['management_api_key']);

    }
}