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
use DooFinderBundle\Merger\AbstractURLProvider;
use DooFinderBundle\Merger\IURLProvider;

/**
 * Class DefaultProductURLProvider
 * @package AppBundle\DooFinder
 */
class DefaultProductURLProvider extends AbstractURLProvider implements IURLProvider
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
         * @var $object DefaultProduct
         *
         */

        // add id
        //if (!array_key_exists('product', $params)) {
        $params['product'] = $object->getId();
        //}

        //add prefix / by default language/shop
        if (!array_key_exists('prefix', $params)) {
            if ($params['document']) {
                $params['prefix'] = substr($params['document']->getFullPath(), 1);
            } else {
                $language = $locale;
                #$language = \Pimcore::getContainer()->get('request_stack')->getCurrentRequest()->getLocale();
                $params['prefix'] = substr($language, 0, 2) . '/shop';
            }
        }

        // add name
        if (!array_key_exists('name', $params)) {
            // add category path
            $category = $object->getFirstCategory();
            if ($category) {
                $path = $category->getNavigationPath($params['rootCategory'], $params['document']);
                $params['name'] = $path . "/";
            }

            // add name
            $name = \Pimcore\File::getValidFilename($object->getOSName());
            $params['name'] .= preg_replace('#-{2,}#', '-', $name);
        }

        unset($params['rootCategory']);
        unset($params['document']);

        // create url
        $urlHelper = \Pimcore::getContainer()->get('pimcore.templating.view_helper.pimcore_url');
        #dump($this->getBaseURL() . $urlHelper($params, $route));

        return $this->getBaseURL() . $urlHelper($params, $route);


    }


}