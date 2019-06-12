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
use Pimcore\Model\DataObject\NewsArticle;
use Sabre\DAV\Exception\NotImplemented;

/**
 * Class NewsArticleURLProvider
 * @package AppBundle\DooFinder
 */
class NewsArticleURLProvider extends AbstractURLProvider implements IURLProvider
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
         * @var $object NewsArticle
         */

        $params = [
            "id" => $object->getId(),
            "text" => $this->getSEOUrl($object->getTitle()), //twig extension
            "prefix" => $prefix // the object doesn't now anything about the document where ist's included!!!
        ];

        $urlHelper = \Pimcore::getContainer()->get('pimcore.templating.view_helper.pimcore_url');
        return $this->getBaseURL() . $urlHelper($params, $route);

    }

    /**
     * @param $string
     * @return string
     */
    private function getSEOUrl($string): string
    {
        //code ducplication see SeoExtension::getSEOURL

        //Lower case everything
        $string = strtolower($string);
        //special characters
        $multi = array(
            "ä" => "ae",
            "ö" => "oe",
            "ß" => "ss",
            "ü" => "ue",
            "Ä" => "ae",
            "Ö" => "oe",
            "ẞ" => "ss",
            "Ü" => "ue",
        );
        $string = str_replace(array_keys($multi), array_values($multi), $string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }
}