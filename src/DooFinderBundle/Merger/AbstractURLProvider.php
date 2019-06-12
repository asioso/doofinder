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
 * Class AbstractURLProvider
 * @package DooFinderBundle\Merger
 */
abstract class AbstractURLProvider implements IURLProvider
{
    /**
     * @var
     */
    private $baseURL;

    /**
     * AbstractURLProvider constructor.
     * @param $baseURL
     */
    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
    }


    abstract public function getUrlForObject($object, $locale, $route, $prefix = null): string;


    /**
     * @return mixed
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }


}