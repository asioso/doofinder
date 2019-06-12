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
 * Class TitleMerger
 * @package AppBundle\DooFinder
 */
class TitleMerger implements IDooFinderItemMerger
{

    public function merge($object, $options = array())
    {
        if ($object instanceof DefaultProduct) {
            $locale = $options[0]['locale'];
            $title = $object->getName($locale);
            //remove all tags
            $title = strip_tags($title);
            //remove line breaks
            $title = preg_replace('/\s+/S', " ", $title);
            //escape / for doofinder feed
            $title = preg_replace("/\//", "//", $title);

            //dump($description);
            return $title;


        }


    }
}