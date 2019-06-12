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

/**
 * Class FirstImageMerger
 * @package AppBundle\DooFinder
 */
class FirstImageMerger implements IDooFinderItemMerger
{


    public function merge($object, $options = array())
    {
        $locale = $options[0]['locale'];
        $thumbnail = $options[0]['thumbnail'];

        if ($object instanceof DefaultProduct) {
            $images = $object->getImages();
            $file = null;

            if ($images->items[0] && $images->items[0]->getImage()) {
                $firstImage = $images->items[0]->getImage();
                $file = $firstImage->getThumbnail($thumbnail);

            } elseif ($object->getBrand() && $object->getBrand()->getImage($locale)) {
                $file = $object->getBrand()->getImage($locale)->getThumbnail($thumbnail);

            } else {
                $file = \Pimcore\Config::getWebsiteConfig()->fallbackImage;
                if ($file) {
                    $file = $file->getThumbnail($thumbnail);
                }
            }

            if ($file) {
                //dump($file->getPath());
                $url = $options[0]['baseUrl'] . $file->getPath();
                return $url;
            }
        }
        return "";
    }
}