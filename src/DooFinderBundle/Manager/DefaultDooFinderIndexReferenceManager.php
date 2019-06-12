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
use Pimcore\Model\DataObject;

/**
 * Class DefaultDooFinderIndexReferenceManager
 * @package DooFinderBundle\Manager
 */
class DefaultDooFinderIndexReferenceManager implements IDooFinderIndexReferenceManager
{

    /**
     * @return null|DataObject\DooFinderIndexReference\Listing
     */
    private function getListing(): DataObject\DooFinderIndexReference\Listing
    {
        try {
            return new DataObject\DooFinderIndexReference\Listing();

        } catch (\Exception $e) {
            //todo: remove
            //throw $e;
            return null;
        }
    }

    /**
     * @return int
     */
    private function getDooFinderFolderId(): int
    {
        $storePath = DataObject::getByPath('/DooFinder');
        if ($storePath instanceof DataObject\Folder) {
            return DataObject::getByPath('/DooFinder')->getId();
        }
        return null;
    }


    public function getAllReferencesInIndex($hashId): array
    {

        $listing = $this->getListing();
        $listing->setCondition(" hashId = ? ", [$hashId]);

        $listing->load();

        return $listing->objects;
    }

    public function getAllReferencesForItem(AbstractDooFinderSearchableItem $item): array
    {
        $listing = $this->getListing();
        $listing->setCondition(" item__Id = ? ", [$item->getId()]);

        $listing->load();

        return $listing->objects;

    }

    public function getAllReferencesForItemAndType(string $type, AbstractDooFinderSearchableItem $item): array
    {
        $listing = $this->getListing();
        $listing->setCondition(" item__Id = ? and dataType = ? ", [$item->getId(), $type]);

        $listing->load();

        return $listing->objects;

    }

    public function getAllReferencesForEngineAndItemAndType(string $engine, string $type, AbstractDooFinderSearchableItem $item): array
    {
        $listing = $this->getListing();
        $listing->setCondition(" hashId = ? and item__Id = ? and dataType = ? ", [$engine, $item->getId(), $type]);
        $listing->load();

        return $listing->objects;

    }


    public function storeReferenceForItemIn($hashId, $dfId, $dataType, AbstractDooFinderSearchableItem $item): AbstractDooFinderIndexReference
    {
        $ref = new DataObject\DooFinderIndexReference();
        $ref->setHashID($hashId);
        $ref->setDfID($dfId);
        $ref->setItem($item);
        $ref->setDataType($dataType);
        $ref->setParentId($this->getDooFinderFolderId());
        $ref->setItem($item);
        $ref->setPublished(true);
        $ref->setKey($hashId . $dataType . $item->getId());
        $ref->save();

        return $ref;
    }

    public function removeAllReferenceForItem(AbstractDooFinderSearchableItem $item): array
    {
        $references = $this->getAllReferencesForItem($item);
        $return = array();
        foreach ($references as $reference) {
            /**
             * @var $reference AbstractDooFinderIndexReference
             */
            if (!isset($return[$reference->getHashID()][$reference->getType()])) {
                $return[$reference->getHashID()][$reference->getType()] = array();
            }
            $return[$reference->getHashID()][$reference->getType()][] = $reference->delete();
        }
        return $return;
    }
}