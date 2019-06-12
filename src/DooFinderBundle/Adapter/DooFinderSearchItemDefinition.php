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
 * Class DooFinderSearchItemDefinition
 * @package DooFinderBundle\Adapter
 */
class DooFinderSearchItemDefinition
{


    /**
     * @var $class string
     */
    public $class;

    /**
     * @var $fields array
     */
    public $fields;
    /**
     * @var $listingClass string
     */
    private $listingClass;

    /**
     * @var $listingArguments array
     */
    private $listingArguments;

    /**
     * DooFinderSearchItemDefinition constructor.
     * @param $class
     * @param $listingClass
     * @param null $listingArguments
     * @param null $fields
     */
    public function __construct($class, $listingClass, $listingArguments = null, $fields = null)
    {
        $this->class = $class;
        if ($fields) {
            $this->fields = $fields;
        } else {
            $this->fields = array();
        }
        $this->listingClass = $listingClass;

        if ($listingArguments) {
            $this->listingArguments = $listingArguments;
        } else {
            $this->listingArguments = array();
        }
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getListingClass(): string
    {
        return $this->listingClass;
    }


    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param DooFinderSearchItemFieldDefinition $defintion
     */
    public function addField(DooFinderSearchItemFieldDefinition $defintion)
    {
        $this->fields[] = $defintion;
    }

    /**
     * @return array
     */
    public function getListingArguments(): array
    {
        $list = array();
        foreach ($this->listingArguments as $argument) {
            $list = array_merge($list, $argument);
        }

        return $list;
    }


}