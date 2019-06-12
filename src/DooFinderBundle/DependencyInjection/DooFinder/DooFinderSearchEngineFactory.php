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


use DooFinderBundle\Adapter\DooFinderSearchEngine;
use DooFinderBundle\Adapter\DooFinderSearchItemDefinition;
use DooFinderBundle\Adapter\DooFinderSearchItemFieldDefinition;
use DooFinderBundle\Configuration\Configuration;
use InvalidArgumentException;

/**
 * Class DooFinderSearchEngineFactory
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
class DooFinderSearchEngineFactory implements IDooFinderSearchEngineFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var array
     */
    private $engines = array();

    /**
     * @var array
     */
    private $hashIdEngines = array();

    /**
     * DooFinderSearchEngineFactory constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {

        $this->configuration = $configuration;
        $this->prepareEngines();
        if (empty($this->engines)) {
            throw new InvalidArgumentException('no search engines defined in "doofinder" configuration');
        }
    }

    /**
     * @param $itemObject
     * @return DooFinderSearchEngine|bool
     *
     */
    public function getEnginesForItem($itemObject): array
    {
        $engines = array();
        foreach ($this->engines as $engine) {
            /**
             * @var $engine DooFinderSearchEngine
             */
            $class = $engine->getDfItemDefinition()->getClass();

            if ($itemObject instanceof $class) {
                $engines[] = $engine;
            }

        }
        return $engines;
    }

    /**
     * @param $hashId
     * @return DooFinderSearchEngine|bool
     */
    public function getEngineByHashId($hashId): DooFinderSearchEngine
    {
        if (!isset($this->hashIdEngines[$hashId])) {
            return false;
        }
        return $this->hashIdEngines[$hashId];

    }

    /**
     *
     */
    private function cleanUp()
    {
        $this->engines = array();
        $this->hashIdEngines = array();
    }

    /**
     *
     */
    private function prepareEngines()
    {
        $this->cleanUp();


        $dooFinderConfigs = $this->configuration->getConfig('search_engines');
        $engineObject = null;
        foreach ($dooFinderConfigs as $key => $engine) {
            $engineObject = $this->buildEngine($engine);
            if ($engineObject->isActive()) {
                $this->engines[] = $engineObject;
                $this->hashIdEngines[$engineObject->getHashId()] = $engineObject;
            }
        }

    }

    /**
     * @param array $config
     * @return DooFinderSearchEngine
     */
    private function buildEngine(array $config): DooFinderSearchEngine
    {
        $name = null;
        $type = null;
        $site_url = null;
        $language = null;
        $currency = null;
        $hashId = null;
        $user = null;
        $item = null;
        $baseUrl = null;
        $active = true;
        $objectPathRegex = null;

        if (isset($config['name'])) {
            $name = $config['name'];
        }
        if (isset($config['type'])) {
            $type = $config['type'];
        }

        if (isset($config['site_url'])) {
            $site_url = $config['site_url'];
        }

        if (isset($config['currency'])) {
            $currency = $config['$urrency'];
        }

        if (isset($config['hashId'])) {
            $hashId = $config['hashId'];
        }

        if (isset($config['user'])) {
            $user = $config['user'];
        }

        if (isset($config['baseURL'])) {
            $baseUrl = $config['baseURL'];
        }

        if (isset($config['active'])) {
            $active = $config['active'];
        }

        if (isset($config['objectPathRegex'])) {
            $objectPathRegex = $config['objectPathRegex'];
        }


        if (isset($config['item'])) {
            $itemClass = $config['item']['class'];
            $listingClass = $config['item']['listing'];
            $listingAttributes = null;
            if (isset($config['item']['listing_arguments'])) {
                $listingAttributes = $config['item']['listing_arguments'];
            }
            $item = new DooFinderSearchItemDefinition($itemClass, $listingClass, $listingAttributes);

            $fieldDefinition = null;
            foreach ($config['item']['fields'] as $field) {
                $fieldDefinition = new DooFinderSearchItemFieldDefinition($field['dfAttribute'], $field['classAttribute'], $field['brickAttribute'], $field['imageAttribute'], $field['locale'], $field['merger'], $field['url'], $field['getter'], $field['prefix'], $field['postfix']);
                $item->addField($fieldDefinition);
            }

        }

        $engine = new DooFinderSearchEngine($name, $type, $site_url, $baseUrl, $language, $currency, $hashId, $user, $item, $active, $objectPathRegex);

        return $engine;
    }

    /**
     * @return array
     */
    public function getRequiredTypesPerEngine(): array
    {
        $types = array();
        foreach ($this->hashIdEngines as $hash => $engine) {
            /**
             * @var $engine DooFinderSearchEngine
             */
            $types[$hash][] = $engine->getType();
        }
        return $types;
    }

    /**
     * @return array
     */
    public function getAllConfiguredEngines(): array
    {
        return $this->engines;
    }

    /**
     * @return array
     */
    public function getWatchedClasses(): array
    {
        $classes = array();
        foreach ($this->engines as $engine) {
            /**
             * @var $engine DooFinderSearchEngine
             */
            $class = $engine->getDfItemDefinition()->getClass();
            $listingClass = $engine->getDfItemDefinition()->getListingClass();
            $listingArguments = $engine->getDfItemDefinition()->getListingArguments();
            if ($engine->isActive()) {
                $classes[$class] = array("class" => $class, "listing" => $listingClass, 'listing_arguments' => $listingArguments);
            }

        }
        return $classes;
    }
}