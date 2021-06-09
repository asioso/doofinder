<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\DependencyInjection\DooFinder;

/**
 * Interface IDooFinderClient
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
interface IDooFinderClient
{

    /**
     * @return \Doofinder\Management\ManagementClient
     */
    public function getClient(): \Doofinder\Management\ManagementClient;

}
