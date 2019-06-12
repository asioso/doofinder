<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\DependencyInjection\DooFinder;


use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;
use DooFinderBundle\Adapter\DooFinderSearchEngine;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class DooFinderService
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
class DooFinderService extends AbstractDooFinderService implements IDooFinderService
{
    /**
     * DooFinderService constructor.
     * @param IDooFinderClient $client
     * @param IDooFinderSearchEngineFactory $configFactory
     * @param RouterInterface $router
     */
    public function __construct(IDooFinderClient $client, IDooFinderSearchEngineFactory $configFactory, RouterInterface $router)
    {
        parent::__construct($client, $configFactory, $router);
    }

    /**
     * @param AbstractDooFinderSearchableItem $item
     * @return bool|DooFinderSearchEngine
     */
    public function getConfiguredDefinitions($item)
    {
        return parent::getConfiguredDefinitions($item);
    }

    /**
     * @param DooFinderSearchEngine $config
     * @param  $item
     * @return array
     * @throws \ReflectionException
     */
    public function parseItemToDefinition(DooFinderSearchEngine &$config, $item): array
    {
        return parent::parseItemToDefinition($config, $item);
    }

    /**
     * @param array $items
     * @return array
     * @throws \ReflectionException
     */
    public function parseItemsToDefinitions(array $items): array
    {
        return parent::parseItemsToDefinitions($items);
    }


    /**
     * @param $item
     * @return array
     */
    public function getConfigurationHeadersForItem($item): array
    {
        return parent::getConfigurationHeaders($item);
    }

    /**
     * @param $item
     * @return array
     * @throws \ReflectionException
     */
    public function getValuesForEngine($item): array
    {
        return parent::getValuesForEnginesByItem($item);
    }

    public function getAllConfiguredEngines(): array
    {
        return parent::getAllConfiguredEnginesFromFactory();
    }
}