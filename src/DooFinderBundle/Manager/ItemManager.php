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

use DooFinderBundle\Manager\IClassManager;

/**
 * Class ItemManager
 * @package DooFinderBundle\Manager
 */
class ItemManager implements IClassManager
{

    /**
     *
     * @param $className
     * @return bool
     */
    public function getItemClassListing($className)
    {

        $listing = 'Pimcore\\Model\\DataObject\\' . ucfirst($className);

        if (!\Pimcore\Tool::classExists($listing)) {
            return false;
        }

        return $listing::getList();
    }

    /**
     * @param $className
     * @return bool|string
     */
    public function getItemClass($className)
    {

        $class = 'Pimcore\\Model\\DataObject\\' . ucfirst($className);

        return $class;
    }
}
