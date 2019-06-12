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

/**
 * Interface IDooFinderSearchEngineFactory
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
interface IDooFinderSearchEngineFactory
{


    /**
     * @param $itemObject
     * @return array <DooFinderSearchEngine>|bool
     *
     */
    public function getEnginesForItem($itemObject): array;

    /**
     * @param $hashId
     * @return DooFinderSearchEngine|bool
     */
    public function getEngineByHashId($hashId): DooFinderSearchEngine;


    /**
     * @return array
     */
    public function getRequiredTypesPerEngine(): array;

    /**
     * @return array
     */
    public function getWatchedClasses(): array;

    /**
     * @return array
     */
    public function getAllConfiguredEngines(): array;
}