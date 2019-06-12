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

/**
 * Interface IDooFinderService
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
interface IDooFinderService
{

    public function addItem(AbstractDooFinderSearchableItem $item): array;

    public function deleteItem(AbstractDooFinderSearchableItem $item): array;

    public function updateItem(AbstractDooFinderSearchableItem $item): array;

    public function addItems(array $items): array;

    public function deleteItems(array $items): array;

    public function updateItems(array $items): array;

    public function getAllItems($engineHash, $type): \Iterator;

    public function getRequiredTypesFromConfigPerEngine(): array;

    public function getAllConfiguredEngines(): array;

    /**
     * @return array
     */
    public function getWatchedClasses(): array;

    /**
     * Get a list of searchengine's types
     *
     * @return array list of types
     */
    public function getDatatypes($hashId);

    /**
     * Get a list of searchengine's types
     *
     * @return array list of types
     */
    public function getTypes($hashId);

    /**
     * Add a type to the searchengine
     *
     * @param string $datatype the type name
     * @return array of searchengine's types
     */
    public function addType($hashId, $datatype);

    /**
     * Delete a type and all its items. HANDLE WITH CARE
     *
     * @param string $datatype the Type to delete. All items belonging
     *                          to that type will be removed. mandatory
     * @return boolean true on success
     */
    public function deleteType($hashId, $datatype);

    /**
     * Obtain stats aggregated data for a certain period.
     *
     * @param \\DateTime $from_date. Starting date. Default is 15 days ago
     * @param \DateTime $to_date . Ending date. Default is today
     *
     * @return ItemsRS iterator through daily aggregated data.
     */
    public function stats($hashId, $from_date = null, $to_date = null);

    /**
     * Obtain frequency sorted list of therms used for a certain period.
     *
     * @param string term: type of term 'clicked', 'searches', 'opportunities'
     *                     - 'clicked': clicked items
     *                     - 'searches': complete searches
     *                     - 'opportunities': searches without results
     * @param \DateTime $from_date . Starting date. Default is 15 days ago
     * @param \DateTime $to_date . Ending date. Default is today
     *
     * @return ItemsResultSet iterator through terms stats.
     */
    public function topTerms($hashId, $term, $from_date = null, $to_date = null);

    /**
     * Ask the server to process the search engine's feeds
     *
     * @return array Assoc array with:
     *               - 'task_created': boolean true if a new task has been created
     *               - 'task_id': if task created, the id of the task.
     */
    public function process($hashId);

    /**
     * Obtain info of the last processing task sent to the server
     *
     * @return array Assoc array with 'state' and 'message' indicating status of the
     *               last asked processing task
     */
    public function processInfo($hashId);

    /**
     * Obtain info about how a task is going or its result
     *
     * @return array Assoc array with 'state' and 'message' indicating the status
     *               of the task
     */
    public function taskInfo($hashId, $taskId);

    /**
     * Obtain logs of the latest feed processing tasks done
     *
     * @return array list of arrays representing the logs
     */
    public function logs($hashId);


    /**
     * @param $object
     * @return bool
     */
    public function isItemObserved($object): bool;

    /**
     * @param $item
     * @return array
     */
    public function getConfigurationHeadersForItem($item): array;

    /**
     * @param $item
     * @return array
     */
    public function getValuesForEngine($item): array;

}