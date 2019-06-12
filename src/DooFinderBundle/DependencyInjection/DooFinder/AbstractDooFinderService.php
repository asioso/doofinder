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


use DateTime;
use Doofinder\Api\Management\ItemsResultSet;
use Doofinder\Api\Management\SearchEngine;
use DooFinderBundle\Adapter\AbstractDooFinderIndexReference;
use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;
use DooFinderBundle\Adapter\DooFinderSearchEngine;
use DooFinderBundle\Adapter\DooFinderSearchItemFieldDefinition;
use DooFinderBundle\Adapter\DooFinderSearchItemFieldMergeDefinition;
use DooFinderBundle\Adapter\DooFinderSearchItemFieldURLDefinition;
use DooFinderBundle\Merger\IDooFinderItemMerger;
use DooFinderBundle\Merger\IURLProvider;
use Exception;
use InvalidArgumentException;
use Iterator;
use Pimcore\Model\Asset\Image;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class DooFinderService
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
abstract class AbstractDooFinderService implements IDooFinderService
{
    /**
     * @var IDooFinderClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $searchEngines;

    /**
     * @var array
     */
    protected $engineMap;

    /**
     * @var IDooFinderSearchEngineFactory
     */
    protected $configFactory;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * DooFinderManagementApi constructor.
     * @param IDooFinderClient $client
     * @param IDooFinderSearchEngineFactory $configFactory
     * @param RouterInterface $router
     */
    public function __construct(IDooFinderClient $client, IDooFinderSearchEngineFactory $configFactory, RouterInterface $router)
    {
        $this->client = $client;
        $this->init();
        $this->configFactory = $configFactory;
        $this->router = $router;
    }

    /**
     * @param  $item
     * @return bool|array<DooFinderSearchEngine>
     */
    protected function getConfiguredDefinitions($item)
    {
        $configs = $this->configFactory->getEnginesForItem($item);

        return $configs;
    }

    /**
     * @return array
     */
    protected function getAllConfiguredEnginesFromFactory()
    {
        return $this->configFactory->getAllConfiguredEngines();
    }

    /**
     * @param $item
     * @return array
     */
    protected function getConfigurationHeaders($item): array
    {
        $configs = $this->getConfiguredDefinitions($item);
        /**
         * @var $config DooFinderSearchEngine
         */
        $header = array();
        foreach ($configs as $config) {
            $data = array();
            #$data[] = AbstractDooFinderSearchableItem::DOO_FINDER_MANUAL_BOOST;
            #$data[] = AbstractDooFinderSearchableItem::DOO_FINDER_INDEXED_TEXT;
            $definition = $config->getDfItemDefinition();

            foreach ($definition->getFields() as $field) {
                /**
                 * @var $field DooFinderSearchItemFieldDefinition
                 */
                $data[] = $field->getDfAttribute();
            }

            $header[$config->getHashId() . $config->getType()] = $data;
        }
        return $header;

    }

    /**
     * @param $item
     * @return array
     *
     * @throws ReflectionException
     */
    protected function getValuesForEnginesByItem($item): array
    {
        $configs = $this->getConfiguredDefinitions($item);
        $result = array();
        /**
         * @var $config DooFinderSearchEngine
         */
        foreach ($configs as $config) {
            if ($config->objectMeetsObjectPathCriteria($item)) {
                $data = $this->parseItemToDefinition($config, $item);
                /**
                 * @var $engine SearchEngine
                 */
                $result[$config->getHashId() . $config->getType()] = $data;
            }

        }
        return $result;

    }


    /**
     * @param DooFinderSearchEngine $config
     * @param  $item
     * @return array
     *
     * @throws ReflectionException
     */
    protected function parseItemToDefinition(DooFinderSearchEngine &$config, $item): array
    {
        $data = array();
        #$data[AbstractDooFinderSearchableItem::DOO_FINDER_ID] = $item->getAllIndexReferences();
        if (method_exists($item, 'getDfManualBoost')) {
            $data[AbstractDooFinderSearchableItem::DOO_FINDER_MANUAL_BOOST] = $item->getDfManualBoost();
        }
        if (method_exists($item, 'getDfIndexedText')) {
            $data[AbstractDooFinderSearchableItem::DOO_FINDER_INDEXED_TEXT] = $item->getDfIndexedText();
        }

        //TODO generate a link to this PAGE/ITEM whatever
        //$data[AbstractDooFinderSearchableItem::DOO_FINDER_ITEM_LINK] = $this->router->generate($config->getItemRoute(), array() );


        $definition = $config->getDfItemDefinition();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($definition->getFields() as $field) {

            /**
             * @var $field DooFinderSearchItemFieldDefinition
             */
            if (null != $merge = $field->getMerge()) {
                /**
                 * @var $merge DooFinderSearchItemFieldMergeDefinition
                 */

                try {
                    $reflection = new ReflectionClass($merge->getClass());
                    $mergerInstance = $reflection->newInstanceArgs();
                    if ($mergerInstance instanceof IDooFinderItemMerger) {
                        if ($field->getClassAttribute() != "self") {
                            $getter = ('get' . ucfirst($field->getClassAttribute()));
                            $items = call_user_func(array($item, $getter));
                        } else {
                            $items = $item;
                        }
                        $data[$field->getDfAttribute()] = $mergerInstance->merge($items, $merge->getOptions());
                    } else {
                        throw new InvalidArgumentException("the Merger Class must implement 'DooFinderBundle\Merger\IDooFinderMerger'");
                    }

                } catch (ReflectionException $e) {
                    //we could handle this here...
                    throw $e;
                }

            } elseif (null != $url = $field->getUrl()) {
                /**
                 * @var $url DooFinderSearchItemFieldURLDefinition
                 */

                try {
                    $reflection = new ReflectionClass($url->getClass());

                    $urlInstance = $reflection->newInstanceArgs(array($config->getBaseUrl()));
                    if ($urlInstance instanceof IURLProvider) {

                        $data[$field->getDfAttribute()] = $urlInstance->getUrlForObject($item, $url->getLocale(), $url->getRoute(), $url->getPrefix());
                    } else {
                        throw new InvalidArgumentException("the URL-Provider Class must implement 'DooFinderBundle\Merger\IURLProvider'");
                    }

                } catch (ReflectionException $e) {
                    //we could handle this here...
                    throw $e;
                }
            } else {
                try {
                    if ($field->getBrickAttribute()) {
                        //BrickAttribute
                        if (!$field->getLocale()) {
                            $attribute = $field->getBrickAttribute();
                            $data[$field->getDfAttribute()] = $propertyAccessor->getValue($item, $attribute);
                        } else {

                            $properties = explode('.', $field->getBrickAttribute());
                            $length = sizeof($properties);
                            $tmpItem = $item;
                            foreach ($properties as $index => $property) {
                                $getter = ('get' . ucfirst($property));
                                if ($length - 1 == $index) {
                                    $data[$field->getDfAttribute()] = call_user_func(array($tmpItem, $getter), $field->getLocale());
                                } else {
                                    $tmpItem = call_user_func(array($tmpItem, $getter));
                                }
                            }
                        }
                    } else if ($field->getClassAttribute()) {
                        //classAttribute
                        if (!$field->getLocale()) {
                            $attribute = $field->getClassAttribute();
                            $data[$field->getDfAttribute()] = $propertyAccessor->getValue($item, $attribute);
                        } else {
                            if ($field->getGetter() != null) {
                                $attribute = $field->getClassAttribute();

                                $subItem = $propertyAccessor->getValue($item, $attribute);
                                if ($subItem) {
                                    $value = call_user_func(array($subItem, $field->getGetter()), $field->getLocale());
                                    $data[$field->getDfAttribute()] = $value;
                                } else {
                                    $data[$field->getDfAttribute()] = null;
                                }
                            } else {
                                $getter = ('get' . ucfirst($field->getClassAttribute()));
                                $string = call_user_func(array($item, $getter), $field->getLocale());
                                if ($field->getPrefix()) {
                                    $string = $field->getPrefix() . $string;
                                }
                                if ($field->getPostfix()) {
                                    $string = $string . $field->getPostfix();
                                }

                                $data[$field->getDfAttribute()] = $string;
                            }
                        }
                    } else if (!empty($field->getImageAttribute())) {
                        $imageAttribute = $field->getImageAttribute()[0];
                        $imagePath = $imageAttribute['field'];
                        try {
                            $image = $propertyAccessor->getValue($item, $imagePath);

                            if (isset($imageAttribute['thumbnail'])) {
                                if ($imageAttribute['thumbnail'] == '~') {
                                    $image = $image->getThumbnail();
                                } else {
                                    $image = $image->getThumbnail($imageAttribute['thumbnail']);
                                }
                            }
                            if ($image) {
                                /**
                                 * @var $image Image|Image\Thumbnail
                                 */
                                $data[$field->getDfAttribute()] = $config->getBaseUrl() . urlencode_ignore_slash($image->getFullPath());
                            }
                        } catch (Exception $e) {
                            //no data for image set
                        }
                    }
                } catch (Exception $e) {
                    throw new InvalidArgumentException(sprintf("something went wrong while preparing data for DooFinder, revise the config for dfAttribute: %s in engine with hashID: %s -  message: %s", $field->getDfAttribute(), $config->getHashId(), $e->getMessage()));
                }
            }
        }

        return array(
            'data' => $data,
            'engine' => $config->getHashId(),
            'type' => $config->getType()
        );
    }

    /**
     * @param array $items
     * @return array
     *
     * @throws ReflectionException
     */
    protected function parseItemsToDefinitions(array $items): array
    {

        $data = array();
        foreach ($items as $object) {
            $configs = $this->getConfiguredDefinitions($object);
            //grouped by engine and type
            foreach ($configs as $config) {
                $data[$config->getHashId()][$config->getType()][] = $this->parseItemToDefinition($config, $object);
            }
        }

        return $data;
    }

    /**
     * @param $hashId
     * @return bool|SearchEngine
     */
    protected function resolveEngine($hashId)
    {
        if (isset($this->engineMap[$hashId])) {
            return $this->engineMap[$hashId];
        }
        return false;
    }

    /**
     * @param AbstractDooFinderSearchableItem $item
     * @return array
     * @throws ReflectionException
     */
    public function getItemFeedEntry(AbstractDooFinderSearchableItem $item)
    {
        $configs = $this->getConfiguredDefinitions($item);
        $result = array();
        foreach ($configs as $config) {
            $data[] = $this->parseItemToDefinition($config, $item);
        }

        return $result;
    }

    /**
     * @param AbstractDooFinderSearchableItem $item
     * @return string
     *
     *
     * @throws ReflectionException
     */
    public function addItem(AbstractDooFinderSearchableItem $item): array
    {
        $configs = $this->getConfiguredDefinitions($item);
        $result = array();
        foreach ($configs as $config) {
            $data = $this->parseItemToDefinition($config, $item);
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($data['engine']);
            $result[$data['engine']] = array("dfId" => $engine->addItem($data['type'], $data['data']), "type" => $data["type"]);
        }

        return $result;
    }

    public function deleteItem(AbstractDooFinderSearchableItem $item): array
    {
        $configs = $this->getConfiguredDefinitions($item);
        $result = array();
        foreach ($configs as $config) {
            $data = $this->parseItemToDefinition($config, $item);
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($data['engine']);

            $result[$data['engine']] = $engine->deleteItem(
                $data['type'],
                $this->resolveDfIdForEngineAndType($item, $data, $engine)
            );

        }

        return $result;
    }

    public function updateItem(AbstractDooFinderSearchableItem $item): array
    {

        $configs = $this->getConfiguredDefinitions($item);
        $result = array();
        foreach ($configs as $config) {
            $data = $this->parseItemToDefinition($config, $item);
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($data['engine']);
            $result[$data['engine']] = array(
                "dfId" => $engine->updateItem(
                    $data['type'],
                    $this->resolveDfIdForEngineAndType($item, $data, $engine),
                    $data['data']
                ),
                "type" => $data["type"]
            );
        }
        return $result;

    }


    public function addItems(array $items): array
    {
        $result = array();
        $data = $this->parseItemsToDefinitions($items);
        foreach ($data as $engineHash => $typedData) {
            $result[$engineHash] = array();
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($engineHash);
            foreach ($typedData as $type => $itemDescriptions) {
                $result[$engineHash][$type] = $engine->addItems($type, $itemDescriptions);
            }
        }

        return $result;

    }

    public function deleteItems(array $items): array
    {
        $result = array();
        $data = $this->parseItemsToDefinitions($items);
        foreach ($data as $engineHash => $typedData) {
            $result[$engineHash] = array();
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($engineHash);
            foreach ($typedData as $type => $itemDescriptions) {
                $ids = array();
                foreach ($itemDescriptions as $description) {
                    $ids[] = $description['id'];
                }
                $result[$engineHash][$type] = $engine->deleteItems($type, $ids);
            }
        }

        return $result;
    }

    public function updateItems(array $items): array
    {
        $result = array();
        $data = $this->parseItemsToDefinitions($items);
        foreach ($data as $engineHash => $typedData) {
            $result[$engineHash] = array();
            /**
             * @var $engine SearchEngine
             */
            $engine = $this->resolveEngine($engineHash);
            foreach ($typedData as $type => $itemDescriptions) {
                $result[$engineHash][$type] = $engine->updateItems($type, $itemDescriptions);
            }
        }

        return $result;
    }

    public function getAllItems($engineHash, $type): Iterator
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$engineHash];
        return $engine->items($type);

    }

    /**
     * @return array
     */
    public function getRequiredTypesFromConfigPerEngine(): array
    {
        return $this->configFactory->getRequiredTypesPerEngine();
    }

    public function getWatchedClasses(): array
    {
        return $this->configFactory->getWatchedClasses();
    }

    /**
     * draw engine map ;)
     */
    protected function init()
    {
        // Get a list of search engines
        $this->searchEngines = $this->client->getClient()->getSearchEngines();
        foreach ($this->searchEngines as $engine) {
            /**
             * @var $engine SearchEngine
             */
            $this->engineMap[$engine->hashid] = $engine;
        }
    }


    /**
     * Get a list of searchengine's types
     *
     * @return array list of types
     */
    public function getDatatypes($hashId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->getDatatypes();
    }

    /**
     * Get a list of searchengine's types
     *
     * @return array list of types
     */
    public function getTypes($hashId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->getTypes();
    }

    /**
     * Add a type to the searchengine
     *
     * @param string $datatype the type name
     * @return array of searchengine's types
     */
    public function addType($hashId, $datatype)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->addType($datatype);
    }

    /**
     * Delete a type and all its items. HANDLE WITH CARE
     *
     * @param string $datatype the Type to delete. All items belonging
     *                          to that type will be removed. mandatory
     * @return boolean true on success
     */
    public function deleteType($hashId, $datatype)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->deleteType($datatype);
    }

    /**
     * Obtain stats aggregated data for a certain period.
     *
     * @param DateTime $from_date . Starting date. Default is 15 days ago
     * @param DateTime $to_date . Ending date. Default is today
     *
     * @return ItemsResultSet iterator through daily aggregated data.
     */
    public function stats($hashId, $from_date = null, $to_date = null)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->stats($from_date, $to_date);
    }

    /**
     * Obtain frequency sorted list of therms used for a certain period.
     *
     * @param string term: type of term 'clicked', 'searches', 'opportunities'
     *                     - 'clicked': clicked items
     *                     - 'searches': complete searches
     *                     - 'opportunities': searches without results
     * @param DateTime $from_date . Starting date. Default is 15 days ago
     * @param DateTime $to_date . Ending date. Default is today
     *
     * @return ItemsResultSet iterator through terms stats.
     */
    public function topTerms($hashId, $term, $from_date = null, $to_date = null)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->topTerms($term, $from_date, $to_date);
    }

    /**
     * Ask the server to process the search engine's feeds
     *
     * @return array Assoc array with:
     *               - 'task_created': boolean true if a new task has been created
     *               - 'task_id': if task created, the id of the task.
     */
    public function process($hashId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->process();
    }

    /**
     * Obtain info of the last processing task sent to the server
     *
     * @return array Assoc array with 'state' and 'message' indicating status of the
     *               last asked processing task
     */
    public function processInfo($hashId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->processInfo();
    }

    /**
     * Obtain info about how a task is going or its result
     *
     * @return array Assoc array with 'state' and 'message' indicating the status
     *               of the task
     */
    public function taskInfo($hashId, $taskId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->taskInfo($taskId);
    }

    /**
     * Obtain logs of the latest feed processing tasks done
     *
     * @return array list of arrays representing the logs
     */
    public function logs($hashId)
    {
        /**
         * @var $engine SearchEngine
         */
        $engine = $this->engineMap[$hashId];
        return $engine->logs();
    }


    public function isItemObserved($object): bool
    {
        return !empty($this->configFactory->getEnginesForItem($object));
    }

    /**
     * @param AbstractDooFinderSearchableItem $item
     * @param array $data
     * @param SearchEngine $engine
     * @return mixed|null
     */
    private function resolveDfIdForEngineAndType(AbstractDooFinderSearchableItem $item, array $data, SearchEngine $engine)
    {
        $reference = $item->getAllReferencesForEngineAndItemAndType($engine->hashid, $data["type"]);
        if (empty($reference)) {
            return null;
        }
        /**
         * @var $ref AbstractDooFinderIndexReference
         */
        $ref = $reference[0];

        return $ref->getDfID();


    }
}