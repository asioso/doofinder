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

use Pimcore\Model\Element\ElementInterface;

/**
 * Class DooFinderSearchEngine
 * @package DooFinderBundle\Adapter
 */
class DooFinderSearchEngine
{
    /**
     * @var $name string
     */
    private $name;

    /**
     * @var $type string
     */
    private $type;

    /**
     * @var $site_url string
     */
    private $site_url;

    /**
     * @var $language string
     */
    private $language;

    /**
     * @var $currency string
     */
    private $currency;

    /**
     * @var $hashId string
     */
    private $hashId;

    /**
     * @var $user string
     */
    private $user;

    /**
     * @var $dfItemDefinition DooFinderSearchItemDefinition
     */
    private $dfItemDefinition;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $objectPathRegex = null;

    /**
     * @var bool
     */
    private $active = true;


    /**
     * DooFinderSearchEngine constructor.
     * @param $name
     * @param $type
     * @param $site_url
     * @param $baseUrl
     * @param $language
     * @param $currency
     * @param $hashId
     * @param $user
     * @param $dfItemDefinition
     * @param $active
     * @param $objectPathRegex
     */
    public function __construct($name, $type, $site_url, $baseUrl, $language, $currency, $hashId, $user, $dfItemDefinition, $active, $objectPathRegex)
    {
        $this->name = $name;
        $this->site_url = $site_url;
        $this->language = $language;
        $this->currency = $currency;
        $this->hashId = $hashId;
        $this->dfItemDefinition = $dfItemDefinition;
        $this->user = $user;
        $this->type = $type;
        $this->baseUrl = $baseUrl;
        $this->objectPathRegex = $objectPathRegex;
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSiteUrl(): string
    {
        return $this->site_url;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getHashId(): string
    {
        return $this->hashId;
    }

    /**
     * @return DooFinderSearchItemDefinition
     */
    public function getDfItemDefinition(): DooFinderSearchItemDefinition
    {
        return $this->dfItemDefinition;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }


    /**
     * @param ElementInterface $object
     * @return bool|false|int
     */
    public function objectMeetsObjectPathCriteria(ElementInterface $object)
    {

        if ($this->objectPathRegex) {
            $augmentedPathRegex = "/" . $this->objectPathRegex . "/";
            return preg_match($augmentedPathRegex, $object->getPath());

        }
        //default case
        return true;
    }


}