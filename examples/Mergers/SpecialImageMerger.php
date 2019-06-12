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
use Pimcore\Model\Asset\Image\Thumbnail;
use Pimcore\Model\DataObject\ProductSpecial;

/**
 * Class SpecialImageMerger
 * @package AppBundle\DooFinder
 */
class SpecialImageMerger implements IDooFinderItemMerger
{


    public function merge($object, $options = array())
    {
        $thumbnail = $options[0]['thumbnail'];

        if ($object instanceof ProductSpecial) {
            $image = $object->getImage();
            if ($image) {
                $file = $image->getThumbnail($thumbnail);
                //dump($file->getPath());
                $url = $options[0]['baseUrl'] . $file->getPath();
                return $url;
            }
        }
        return "";
    }
}