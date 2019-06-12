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


use AppBundle\Model\DefaultProduct;
use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;
use DooFinderBundle\Adapter\DooFinderSearchEngine;

/**
 * Class DefaultDooFinderServiceHandler
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
class DefaultDooFinderServiceHandler implements IDooFinderServiceHandler
{
    /**
     * @var IDooFinderService
     */
    private $service;

    /**
     * DefaultDooFinderServiceHandler constructor.
     * @param IDooFinderService $service
     */
    public function __construct(IDooFinderService $service)
    {

        $this->service = $service;
    }


    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleUpdate(AbstractDooFinderSearchableItem $object)
    {


        if ($this->service->isItemObserved($object) && $object->isPublished() && $object->getShopProduct()) {
            if (empty($object->getAllIndexReferences())) {
                //item is freshly published
                $references = $this->service->addItem($object);
                die;
                foreach ($references as $engineHash => $referenceData) {
                    $object->addNewReference($engineHash, $referenceData["type"], $referenceData["dfId"]);
                }
            } else {
                //update item
                $references = $this->service->updateItem($object);
                //update newly added references
                foreach ($references as $engineHash => $referenceData) {
                    if (empty($object->getAllReferencesForEngineAndItemAndType($engineHash, $referenceData["type"]))) {
                        $object->addNewReference($engineHash, $referenceData["type"], $referenceData['dfId']);
                    }
                }
            }
        } elseif ($this->service->isItemObserved($object) && !$object->isPublished()) {
            //check if object has references

            if (!empty($object->getAllIndexReferences())) {
                //remove item from all indices
                $result = $this->service->deleteItem($object);
                #dump($result);
                $object->removeAllReferences();
            }
            //do nothing otherwise
        }


    }

    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleDelete(AbstractDooFinderSearchableItem $object)
    {
        if ($this->service->isItemObserved($object) && $object->isPublished()) {
            $object->removeAllReferences();
            $this->service->deleteItem($object);
        }
    }

    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleNew(AbstractDooFinderSearchableItem $object)
    {
        if ($this->service->isItemObserved($object) && $object->isPublished()) {
            //add item
            $references = $this->service->addItem($object);
            foreach ($references as $engineHash => $referenceData) {
                $object->addNewReference($engineHash, $referenceData["type"], $referenceData["dfId"]);
            }

        }
    }

    /**
     * @return array
     */
    public function getWatchedClasses(): array
    {
        return $this->service->getWatchedClasses();
    }

    /***
     * @param AbstractDooFinderSearchableItem $object
     * @return array
     */
    public function getValuesForEngine($object): array
    {

        if ($this->service->isItemObserved($object) && $object->isPublished()) {

            return $this->service->getValuesForEngine($object);

        }
        return array();

    }

    /**
     * @param  $object
     * @return array
     */
    public function getHeaderForEngine($object): array
    {
        return $this->service->getConfigurationHeadersForItem($object);
    }

    /**
     * @return array
     */
    public function getConfiguredEngines(): array
    {
        return $this->service->getAllConfiguredEngines();
    }

    /**
     * @return array
     */
    public function startProcessingForConfiguredEngines(): array
    {
        $ids = array();
        foreach ($this->getConfiguredEngines() as $engine) {
            /**
             * @var $engine DooFinderSearchEngine
             */
            if (!isset($ids[$engine->getHashId()])) {
                $ids[$engine->getHashId()] = true;
            }
        }
        $response = array();
        foreach ($ids as $hashId => $value) {
            $response[$hashId] = $this->service->process($hashId);
        }
        return $response;
    }
}