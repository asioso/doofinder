<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Adapter;

/**
 * Class DooFinderSearchItemFieldDefintion
 * @package DooFinderBundle\Adapter
 */
class DooFinderSearchItemFieldURLDefinition
{
    /**
     * @var
     */
    private $class;
    /**
     * @var
     */
    private $locale;
    /**
     * @var
     */
    private $prefix;

    /**
     * @var
     */
    private $route;


    /**
     * DooFinderSearchItemFieldMergeDefintion constructor.
     * @param $class
     * @param $locale
     * @param $prefix
     * @param $route
     */
    public function __construct($class, $locale, $prefix, $route)
    {
        $this->class = $class;
        $this->locale = $locale;
        $this->prefix = $prefix;
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }


}