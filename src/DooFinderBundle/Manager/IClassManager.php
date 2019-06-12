<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\DependencyInjection;


/**
 * Interface IClassManager
 * @package DooFinderBundle\DependencyInjection
 */
interface IClassManager
{

    /**
     * @param $className
     * @return bool
     */
    public function getItemClassListing($className);

    /**
     * @param $className
     * @return bool|string
     */
    public function getItemClass($className);

}