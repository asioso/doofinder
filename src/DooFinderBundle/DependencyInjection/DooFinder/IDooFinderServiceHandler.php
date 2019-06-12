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
 * Interface IDooFinderServiceHandler
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
interface IDooFinderServiceHandler
{
    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleUpdate(AbstractDooFinderSearchableItem $object);

    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleDelete(AbstractDooFinderSearchableItem $object);

    /**
     * @param AbstractDooFinderSearchableItem $object
     */
    public function handleNew(AbstractDooFinderSearchableItem $object);

    /**
     * @return array
     */
    public function getWatchedClasses(): array;

    /**
     * @param AbstractDooFinderSearchableItem $object
     * @return array
     */
    public function getValuesForEngine($object): array;

    /**
     * @param $hashId
     * @param $dataType
     * @return array
     */
    public function getHeaderForEngine($object): array;

    /**
     * @return array
     */
    public function getConfiguredEngines(): array;

    /**
     * @param array $engineConfigArray
     */
    public function startProcessingForConfiguredEngines(): array;

}