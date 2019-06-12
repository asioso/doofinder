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


use DooFinderBundle\Merger\AbstractURLProvider;
use DooFinderBundle\Merger\IURLProvider;
use Pimcore\Model\DataObject\ProductSpecial;

/**
 * Class ProductSpecialURLProvider
 * @package AppBundle\DooFinder
 */
class ProductSpecialURLProvider extends AbstractURLProvider implements IURLProvider
{

    /**
     * @param $object
     * @param $locale
     * @param $route
     * @param null $prefix
     * @return string
     */
    public function getUrlForObject($object, $locale, $route, $prefix = null): string
    {
        /**
         * @var $object ProductSpecial
         */
        if ($link = $object->getDfLink($locale)) {
            $dataLink = $link->getPath();

            if (preg_match("/^\/(\\w+)/", $dataLink) && $prefix) {
                return $prefix . $dataLink;
            } else {
                return $dataLink;
            }

        }
        //dump($object->getLink($locale)->direct);
        $dataLink = $object->getLink($locale);
        if ($dataLink) {
            return $dataLink->getPath();
        } else return "";


    }
}