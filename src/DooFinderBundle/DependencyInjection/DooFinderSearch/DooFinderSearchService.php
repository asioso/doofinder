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
use Doofinder\Api\Search\Error;
use Psr\Log\LoggerInterface;

/**
 * Class DooFinderSearchService
 * @package DooFinderBundle\DependencyInjection\DooFinderSearch
 */
class DooFinderSearchService implements IDooFinderSearchService
{

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client = null;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function init($hashId)
    {
        try {
            $this->client = new Client($hashId, $this->apiKey);
        } catch (Error $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }


}