<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\DependencyInjection\DooFinderSearch;


use Doofinder\Api\Search\Client;

/**
 * Interface IDooFinderSearchService
 * @package DooFinderBundle\DependencyInjection\DooFinderSearch
 */
interface IDooFinderSearchService
{

    /**
     * @return Client
     */
    public function getClient(): Client;

    /**
     * @param $hashId
     * @return  void
     */
    public function init($hashId);


}