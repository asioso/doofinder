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


use Doofinder\Api\Management\Errors\InvalidApiKey;
use Psr\Log\LoggerInterface;

/**
 * Class DooFinderClient
 * @package DooFinderBundle\DependencyInjection\DooFinder
 */
class DooFinderClient implements IDooFinderClient
{

    /**
     * @var \Doofinder\Api\Management\Client
     */
    private $client = null;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var
     */
    private $apiKey;

    /**
     * DooFinderClient constructor.
     * @param $apiKey
     * @param LoggerInterface $logger
     */
    public function __construct($apiKey, LoggerInterface $logger)
    {
        $this->logger = $logger;


        $this->apiKey = $apiKey;
    }

    private function init($apiKey)
    {
        try {
            // Instantiate the object, use your Doofinder's API_KEY.
            $this->client = new \Doofinder\Api\Management\Client($apiKey);

        } catch (InvalidApiKey $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return \Doofinder\Api\Management\Client
     */
    public function getClient(): \Doofinder\Api\Management\Client
    {
        if (!$this->client) {
            $this->init($this->apiKey);
        }
        return $this->client;
    }


}