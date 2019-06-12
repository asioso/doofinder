<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Merger;

/**
 * Interface IURLProvider
 * @package DooFinderBundle\Merger
 */
interface IURLProvider
{
    /**
     * @param $object
     * @param $locale
     * @param $route
     * @param null $prefix
     * @return string
     */
    public function getUrlForObject($object, $locale, $route, $prefix = null): string;

}