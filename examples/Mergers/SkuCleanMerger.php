<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace AppBundle\DooFinder;


use DooFinderBundle\Merger\IDooFinderItemMerger;

/**
 * Class SkuCleanMerger
 * @package AppBundle\DooFinder
 */
class SkuCleanMerger implements IDooFinderItemMerger
{

    public function merge($object, $options = array())
    {
        if (!empty($object)) {
            //escape slashes in skus
            $escapedObject = preg_replace("/\//", "//", $object);
            return $escapedObject;
        }

        return $object;

    }
}