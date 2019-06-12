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
 * Class DefaultProductAttributeMerger
 * @package AppBundle\DooFinder
 */
class DefaultProductAttributeMerger implements IDooFinderItemMerger
{

    public function merge($object, $locale = null)
    {

        /**
         * @var $object DefaultProduct
         */
        $data = array();
        if ($object->getLength()) {
            $data[] = "Länge= " . $object->getLength()->getValue() . "" . $object->getLength()->getUnit()->abbreviation;
        }
        if ($object->getWidth()) {
            $data[] = "Breite= " . $object->getWidth()->getValue() . "" . $object->getWidth()->getUnit()->abbreviation;
        }
        if ($object->getHeight()) {
            $data[] = "Höhe= " . $object->getHeight()->getValue() . "" . $object->getHeight()->getUnit()->abbreviation;
        }
        if ($object->getWeight()) {
            $data[] = "Gewicht= " . $object->getWeight()->getValue() . "" . $object->getWeight()->getUnit()->abbreviation;
        }

        return implode("/", $data);

    }
}