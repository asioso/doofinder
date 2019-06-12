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
 * Class UvpPriceMerger
 * @package AppBundle\DooFinder
 */
class UvpPriceMerger implements IDooFinderItemMerger
{

    public function merge($object, $options = array())
    {
        if ($object instanceof DefaultProduct) {
            $price = $object->getPriceOld($options[0]['locale']);
            //return number_format((float)$price, 2, ',', '.') . " EUR";

            if (isset($options[0]['currency'])) {
                $price = number_format((float)$price, 2, '.', '');
                $price = $price . " " . $options[0]['currency'];
            }
            return $price;
        }

        return "";
    }
}