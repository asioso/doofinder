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
 * Class DooFinderSearchItemFieldMergeDefinition
 * @package DooFinderBundle\Adapter
 */
class DooFinderSearchItemFieldMergeDefinition
{
    /**
     * @var
     */
    private $class;
    /**
     * @var
     */
    private $options;


    /**
     * DooFinderSearchItemFieldMergeDefintion constructor.
     * @param $class
     * @param $options
     */
    public function __construct($class, $options)
    {
        ;
        $this->class = $class;
        $this->options = $options;
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
    public function getOptions()
    {
        return $this->options;
    }

}