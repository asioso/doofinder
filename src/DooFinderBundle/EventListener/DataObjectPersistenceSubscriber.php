<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */


namespace DooFinderBundle\EventListener;


use DooFinderBundle\Adapter\AbstractDooFinderSearchableItem;
use DooFinderBundle\DependencyInjection\DooFinder\IDooFinderServiceHandler;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DataObjectPersistenceSubscriber
 * @package DooFinderBundle\EventListener
 */
class DataObjectPersistenceSubscriber implements EventSubscriberInterface
{

    /**
     * @var IDooFinderServiceHandler
     */
    private $service;

    /**
     * DataObjectPersistenceSubscriber constructor.
     * @param IDooFinderServiceHandler $service
     */
    public function __construct(IDooFinderServiceHandler $service)
    {

        $this->service = $service;

    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
            'pimcore.dataobject.preSave' => 'onPreSafe',
            'pimcore.dataobject.preDelete' => 'onPreDelete'
        );

    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof AbstractDooFinderSearchableItem) {
            $this->service->handleUpdate($object);
        }

    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPreSafe(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof AbstractDooFinderSearchableItem) {

            $this->service->handleNew($object);
        }
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPreDelete(DataObjectEvent $event)
    {
        //TODO
        $object = $event->getObject();
        if ($object instanceof AbstractDooFinderSearchableItem) {
            $this->service->handleDelete($object);
        }
    }
}