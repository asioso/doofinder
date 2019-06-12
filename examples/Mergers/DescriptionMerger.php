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


use AppBundle\Model\DefaultProduct;
use DooFinderBundle\Merger\IDooFinderItemMerger;

/**
 * Class DescriptionMerger
 * @package AppBundle\DooFinder
 */
class DescriptionMerger implements IDooFinderItemMerger
{

    public function merge($object, $options = array())
    {
        if ($object instanceof DefaultProduct) {
            $locale = $options[0]['locale'];
            $description = $object->getDescription($locale);
            //remove all tags
            $description = strip_tags($description);
            //remove line breaks
            $description = preg_replace('/\s+/S', " ", $description);
            //escape / for doofinder feed
            //$description = preg_replace("/\//", "//", $description);

            //dump($description);
            return $description;


        }


    }
}