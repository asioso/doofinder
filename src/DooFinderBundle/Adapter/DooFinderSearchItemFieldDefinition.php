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
class DooFinderSearchItemFieldDefinition
{
    /**
     * @var
     */
    private $dfAttribute;

    /**
     * @var
     */
    private $classAttribute;

    /**
     * @var
     */
    private $brickAttribute;

    /**
     * @var
     */
    private $imageAttribute;

    /**
     * @var
     */
    private $locale;

    /**
     * @var
     */
    private $merge;
    /**
     * @var null
     */
    private $url;

    /**
     * @var
     */
    private $getter;
    /**
     * @var null
     */
    private $prefix;
    /**
     * @var null
     */
    private $postfix;

    /**
     * DooFinderSearchItemFieldDefinition constructor.
     * @param $dfAttribute
     * @param $classAttribute
     * @param $brickAttribute
     * @param $imageAttribute
     * @param $locale
     * @param null $merge
     * @param null $url
     * @param null $getter
     */
    public function __construct($dfAttribute, $classAttribute, $brickAttribute, $imageAttribute, $locale, $merge = null, $url = null, $getter = null, $prefix = null, $postfix = null)
    {

        $this->dfAttribute = $dfAttribute;
        $this->classAttribute = $classAttribute;
        $this->locale = $locale;
        //use only first for now
        if (isset($merge[0])) {
            $merge = $merge[0];
            $this->merge = new DooFinderSearchItemFieldMergeDefinition($merge['class'], $merge['options']);
        }
        $this->brickAttribute = $brickAttribute;
        $this->imageAttribute = $imageAttribute;

        if (isset($url[0])) {
            $url = $url[0];
            $this->url = new DooFinderSearchItemFieldURLDefinition($url['class'], $url['locale'], $url['prefix'], $url['route']);
        }

        $this->getter = $getter;
        $this->prefix = $prefix;
        $this->postfix = $postfix;
    }

    /**
     * @return mixed
     */
    public function getDfAttribute()
    {
        return $this->dfAttribute;
    }

    /**
     * @return mixed
     */
    public function getClassAttribute()
    {
        return $this->classAttribute;
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
    public function getMerge()
    {
        return $this->merge;
    }

    /**
     * @return mixed
     */
    public function getBrickAttribute()
    {
        return $this->brickAttribute;
    }

    /**
     * @return mixed
     */
    public function getImageAttribute()
    {
        return $this->imageAttribute;
    }

    /**
     * @return null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * @return null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return null
     */
    public function getPostfix()
    {
        return $this->postfix;
    }


}