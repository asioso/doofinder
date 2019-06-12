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

use DooFinderBundle\Manager\IDooFinderIndexReferenceManager;


/**
 * Class AbstractDooFinderSearchableProductProduct
 * @package DooFinderBundle\Adapter
 */
abstract class AbstractDooFinderSearchableItem extends AbstractDooFinderProduct
{


    /**
     * @return AbstractDooFinderIndexReference[]
     */
    public function getAllIndexReferences(): array
    {
        /**
         * @var IDooFinderIndexReferenceManager $referenceManager
         */
        $referenceManager = \Pimcore::getContainer()->get('dfb.reference.manager');

        return $referenceManager->getAllReferencesForItem($this);
    }


    public function getAllReferencesForItemAndType(string $type): array
    {
        /**
         * @var IDooFinderIndexReferenceManager $referenceManager
         */
        $referenceManager = \Pimcore::getContainer()->get('dfb.reference.manager');

        return $referenceManager->getAllReferencesForItemAndType($type, $this);
    }

    public function getAllReferencesForEngineAndItemAndType(string $engineHashId, string $type): array
    {
        /**
         * @var IDooFinderIndexReferenceManager $referenceManager
         */
        $referenceManager = \Pimcore::getContainer()->get('dfb.reference.manager');

        return $referenceManager->getAllReferencesForEngineAndItemAndType($engineHashId, $type, $this);
    }

    public function addNewReference($hashId, $dataTye, $dfId): AbstractDooFinderIndexReference
    {
        /**
         * @var IDooFinderIndexReferenceManager $referenceManager
         */
        $referenceManager = \Pimcore::getContainer()->get('dfb.reference.manager');

        return $referenceManager->storeReferenceForItemIn($hashId, $dfId, $dataTye, $this);
    }

    public function removeAllReferences(): array
    {
        /**
         * @var IDooFinderIndexReferenceManager $referenceManager
         */
        $referenceManager = \Pimcore::getContainer()->get('dfb.reference.manager');

        return $referenceManager->removeAllReferenceForItem($this);
    }


    /**
     * @param array $params
     * @param string $route
     * @param bool|true $reset
     *
     * @return string
     */
    public function getLocalizedShopUrl(array $params = [], $route = 'shop-detail', $reset = true)
    {
        // add id
        if (!array_key_exists('product', $params)) {
            $params['product'] = $this->getId();
        }

        //add prefix / by default language/shop
        if (!array_key_exists('prefix', $params)) {
            if ($params['document']) {
                $params['prefix'] = substr($params['document']->getFullPath(), 1);
            } else {
                $language = \Pimcore::getContainer()->get('request_stack')->getCurrentRequest()->getLocale();
                $params['prefix'] = substr($language, 0, 2) . '/shop';
            }
        }

        // add name
        if (!array_key_exists('name', $params)) {
            // add category path
            $category = $this->getFirstCategory();
            if ($category) {
                $path = $category->getNavigationPath($params['rootCategory'], $params['document']);
                $params['name'] = $path . "/";
            }

            // add name
            $name = \Pimcore\File::getValidFilename($this->getOSName());
            $params['name'] .= preg_replace('#-{2,}#', '-', $name);
        }

        unset($params['rootCategory']);
        unset($params['document']);

        // create url
        $urlHelper = \Pimcore::getContainer()->get('pimcore.templating.view_helper.pimcore_url');

        return $urlHelper($params, $route, $reset);
    }


}