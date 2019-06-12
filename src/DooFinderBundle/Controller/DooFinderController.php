<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\Controller;


use DooFinderBundle\File\FileHandler;
use Pimcore\Controller\FrontendController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class DooFinderController
 * @package DooFinderBundle\Controller
 */
class DooFinderController extends FrontendController
{

    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * DooFinderController constructor.
     * @param FileHandler $fileHandler
     */
    public function __construct(FileHandler $fileHandler)
    {

        $this->fileHandler = $fileHandler;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // set auto-rendering to twig
        $this->setViewAutoRender($event->getRequest(), true, 'twig');
    }

    /**
     * @param $hashID
     * @param $locale
     * @param $zone
     * @param $selector
     * @return mixed
     */
    public function doofinderLayerAction($hashID, $locale, $zone, $selector)
    {
        //search engine id, locale, box selector as params
        $params = array(
            "hashID" => $hashID,
            "language" => $locale,
            "selector" => $selector,
            "zone" => $zone,
        );
        return $this->render(':DooFinder/Layer:doofinderLayer.html.twig', $params);
    }


    /**
     * @param Request $requests
     * @param $hashId
     * @param $type
     * @return Response
     *
     * @\Symfony\Component\Routing\Annotation\Route("/asioso-doofinder-bundle/{hashId}/feed/{type}")
     */
    public function dataFeedAction(Request $request, $hashId, $type)
    {
        //find latest feed file for engine and type
        try {
            $file = $this->fileHandler->findLatestFileForEngine($hashId, $type);
            return $this->file($file);

        } catch (FileNotFoundException $e) {
            return new Response("", 404);
        }


    }

}
