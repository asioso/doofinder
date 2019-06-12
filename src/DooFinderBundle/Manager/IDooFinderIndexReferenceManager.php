<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Manager;

use DooFinderBundle\Adapter\AbstractDooFinderIndexReference;
use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;

/**
 * Interface IDooFinderIndexReferenceManager
 * @package DooFinderBundle\Manager
 */
interface IDooFinderIndexReferenceManager
{
    public function getAllReferencesInIndex($hashId): array;

    public function getAllReferencesForItem(AbstractDooFinderSearchableItem $item): array;

    public function getAllReferencesForItemAndType(string $type, AbstractDooFinderSearchableItem $item): array;

    public function getAllReferencesForEngineAndItemAndType(string $engine, string $type, AbstractDooFinderSearchableItem $item): array;

    public function storeReferenceForItemIn($hashId, $dfId, $dataType, AbstractDooFinderSearchableItem $item): AbstractDooFinderIndexReference;

    public function removeAllReferenceForItem(AbstractDooFinderSearchableItem $item): array;

}