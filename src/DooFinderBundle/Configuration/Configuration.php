<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Configuration;

/**
 * Class Configuration
 * @package DooFinderBundle\Configuration
 */
class Configuration
{
    const SYSTEM_CONFIG_FILE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DooFinderBundle/config.yml';

    /**
     * @var array
     */
    protected $config;


    /**
     * @param array $config
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfigNode()
    {
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function getConfigArray()
    {
        return $this->config;
    }

    /**
     * @param $slot
     *
     * @return mixed
     */
    public function getConfig($slot)
    {
        return $this->config[$slot];
    }


}