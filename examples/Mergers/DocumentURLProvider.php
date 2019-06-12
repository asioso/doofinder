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
use Pimcore\Model\Document;

/**
 * Class ProductSpecialURLProvider
 * @package AppBundle\DooFinder
 */
class DocumentURLProvider extends AbstractURLProvider implements IURLProvider
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
         * @var $object Document
         */
        //dump($object->getLink($locale)->direct);
        $dataLink = $object->getFullPath();
        if ($dataLink) {
            return $prefix . $dataLink;
        } else return "";


    }
}