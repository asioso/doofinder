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


use AppBundle\Model\ShopCategory;
use DooFinderBundle\Merger\IDooFinderItemMerger;

/**
 * Class MetadataMerger
 * @package AppBundle\DooFinder
 */
class MetadataMerger implements IDooFinderItemMerger
{


    public function merge($objects, $options = array())
    {
        if (empty($objects)) {
            return "";
        }

        return implode(' %% ', $objects);

    }
}