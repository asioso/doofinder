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

use Pimcore\Model\DataObject\Concrete;

/**
 * Class AbstractDooFinderIndexReference
 * @package DooFinderBundle\Adapter
 */
abstract class AbstractDooFinderIndexReference extends Concrete
{
    /**
     * @var string
     */
    protected $hashID;

    /**
     * @var string
     */
    protected $dfID;

    /**
     * @var $string
     */
    protected $dataType;

    /**
     * @var Concrete
     */
    protected $item;


    /**
     * @return mixed
     */
    public function getHashID()
    {
        return $this->hashID;
    }

    /**
     * @return mixed
     */
    public function getDfID()
    {
        return $this->dfID;
    }

    /**
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return Concrete
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param string $hashID
     */
    public function setHashID($hashID)
    {
        $this->hashID = $hashID;
    }

    /**
     * @param string $dfID
     */
    public function setDfID($dfID)
    {
        $this->dfID = $dfID;
    }

    /**
     * @param mixed $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * @param Concrete $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }


}