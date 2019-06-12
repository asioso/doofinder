<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Adapter;

/**
 * Interface IDooFinderSearchableItem
 * @package DooFinderBundle\Adapter
 */
interface IDooFinderSearchableItem
{


    /**
     * @return mixed
     */
    public function getDfManualBoost();

    /**
     * @param mixed $dfManualBoost
     */
    public function setDfManualBoost($dfManualBoost);

    /**
     * @return mixed
     */
    public function getDfIndexedText();

    /**
     * @param mixed $dfIndexedText
     */
    public function setDfIndexedText($dfIndexedText);

    /**
     * @return mixed
     */
    public function getDfUrl();

    /**
     * @param mixed $dfUrl
     */
    public function setDfUrl($dfUrl);


    /**
     * @return AbstractDooFinderIndexReference[]
     */
    public function getAllIndexReferences(): array;


    public function getAllReferencesForItemAndType(string $type): array;

    public function getAllReferencesForEngineAndItemAndType(string $engineHashId, string $type): array;

    public function addNewReference($hashId, $dataTye, $dfId): AbstractDooFinderIndexReference;

    public function removeAllReferences(): array;


}